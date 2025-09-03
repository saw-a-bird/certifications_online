<?php

namespace App\Command;

use ReflectionClass;   // ✅ add this
use App\Entity\Certification;
use App\Entity\eProvider;
use App\Entity\Exam;
use App\Entity\ExamPaper;
use App\Entity\Source;
use App\Repository\SourceRepository;
use App\Services\PDFManager;
use App\Services\PDFImporter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

//https://symfony.com/doc/5.4/the-fast-track/en/24-cron.html#setting-up-a-cron-on-platform-sh

class ScraperCommand extends Command
{
    protected static $defaultName = 'app:scraper:gratisexams';
    const SOURCE_URL = "https://www.gratisexam.com";
    const MAX_TRIES = 99;
    
    private $batchSize = 0;
    private $counter = 0;
    private $testMode = false;
    private Client $client;
    public SymfonyStyle $io;
    public $pdfManager, $importerService, $sourcesRepository;
    public $proxyListDirectory, $countProxies, $triedCount, $currentProxyIndex;
    public $proxy = null;
    public $proxyArray = array();
    public $entityManager;
    public $sourcesFlushed = 0;

    public function __construct($proxyListDirectory, EntityManagerInterface $entityManager, PDFManager $pdfManager, PDFImporter $importerService, SourceRepository $sourcesRepository)
    {   
        ini_set('memory_limit', '-1');

        $this->proxyListDirectory = $proxyListDirectory;
        $this->entityManager = $entityManager;
        $this->pdfManager = $pdfManager;
        $this->importerService = $importerService;
        $this->sourcesRepository = $sourcesRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Scraps the gratis-exams website for certification exams.')
            ->addOption('provider', "p", InputOption::VALUE_OPTIONAL, 'Starting from this provider', null)
            ->addOption('only', "o", InputOption::VALUE_OPTIONAL, 'Only this provider', false)
            ->addOption('test', "t", InputOption::VALUE_OPTIONAL, 'No persist', false)
            ->addOption('batch', "b", InputOption::VALUE_OPTIONAL, 'Batch size', 5);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->testMode = $input->getOption('test');
        $providerOption = $input->getOption('provider');
        $onlyOption = $input->getOption('only');
        $continue = false;

        // $connection = $this->entityManager->getConnection();
        // $connection->beg();
        try {
            $this->getProxyList();
            $this->newProxy();

            $this->io->warning("Connecting...");
            $crawlerRoot = $this->getResponse(self::SOURCE_URL);
            $this->io->success("Successfully connected ". $this->proxy );

            $this->batchSize = $input->getOption('batch');

            //Provider
            $crawlerRoot->filter('td a')->each(function ($node) use ($providerOption, $onlyOption, $continue) {
                if ($providerOption == null || ($providerOption == $node->text() || $continue)) {
                    if ($providerOption != null && $continue == false && $onlyOption === false) {
                        $continue = true;
                    }

                    $provider = $this->skeptic_check(eProvider::class, ['name' => $node->text()])->setName($node->text());
                    $this->entityManager->persist($provider);

                    $this->io->info("Found - Provider: ".$provider->getName()." (Saved: ".$provider->getId().")");
                    
                    // //Exams
                    $examArray = array();
                    $this->getResponse(self::SOURCE_URL."/".$node->attr('href'))->filter('tbody tr')->each(function ($examRow) use(&$provider, &$examArray) {
                        $childTag = $examRow->filter('td a');
                        $examTitle = $examRow->filter('td > strong')->text();
                        
                        $examCode = strtoupper($childTag->text());
                        $exam =  $this->skeptic_check(Exam::class, ['code' => $examCode, 'eProvider' => $provider], $provider->getId() != null)
                                ->setEProvider($provider)
                                ->setCode($examCode)
                                ->setTitle($examTitle);
                        
                        if (!isset($examArray[$examTitle])) {
                            $examArray[$examTitle] = array();
                        }

                        if (!isset($examArray[$examTitle][$exam->getCode()])) {
                            $examArray[$examTitle][$exam->getCode()] = $exam;
                            $this->io->text("Exam: ".$exam);
                            $this->entityManager->persist($exam);
                                //Papers
                            if (intval($examRow->filter("td:nth-child(3)")->text()) > 0) {
                                $this->getResponse(self::SOURCE_URL.$childTag->attr('href'))->filter('tbody tr')->each(function ($paperRow, $key) use(&$exam, &$examId) {

                                    
                                    $pdfLink = $paperRow->filter("#pdf_link");
                                    if ($pdfLink->count() > 0) {
                                        
                                        $fullName = $paperRow->filter('td > strong')->text();  
                                        $this->io->warning("Paper #$key - ".$fullName);

                                        $namePaper = explode(".", $fullName);
                                        $examPaper = $this->skeptic_check(ExamPaper::class, ['importedFrom' => $namePaper[5]], $exam->getId() != null)
                                                    ->setExam($exam)
                                                    ->setQProvider($namePaper[1])
                                                    ->setUpdatedAt(
                                                        DateTime::createFromFormat(
                                                            'Y-m-d', 
                                                            substr($namePaper[3], 1)
                                                        )
                                                    );
                                        
                                        if (isset($namePaper[5])) {
                                            $examPaper->setImportedFrom($namePaper[5]);
                                        }

                                        $this->entityManager->persist($examPaper);

                                        // Questions
                                        $this->getSource("GratisExam.".$fullName.".pdf", array(
                                                "examPaper" => $examPaper,
                                                "pdfLink" => $pdfLink->attr("href")
                                            )
                                        );

                                    } // else empty node list (no Link, only vce) -- don't save.
                                });
                            } else {
                                $this->io->warning("No paper found.");
                            }
                        } else {
                            $this->io->text("Already saved. Ignored exam.");
                        }
                    });
                    
                    $this->io->success("Found ".count($examArray)." exams");

                    $foundCertif = 0;
                    foreach ($examArray as $title => $array) {
                        if (count($array) >= 2) {
                            $certification = $this->skeptic_check(Certification::class, ['title' => $title, 'eProvider' => $provider], $provider->getId() != null)->setTitle($title)->setEProvider($provider);

                            foreach ($array as $exam) {
                                $certification->addExam($exam);
                            }

                            $this->entityManager->persist($certification);
                            $foundCertif++;
                        }
                        
                    }

                    if ($foundCertif > 0) {
                        $this->io->success("Created ".$foundCertif." certification(s)");
                    }

                    // $this->counter++;
                    if ($this->testMode == false) {
                        $this->io->success("Persisting..."); 
                        $this->entityManager->flush();  // write current batch to DB
                        $this->entityManager->clear();  // clear to save memory
                        $this->io->success("Persisted and flushed!"); 
                        $this->sourcesFlushed += 1;
                    }
                }
            });

        } catch (Exception $e) {
            // if ($this->testMode == false) {
            //     $connection->rollback();
            // }
            $this->entityManager->clear();
            $this->io->error("Line ".$e->getLine()." - ".$e->getMessage());
            $this->io->error($e->getTraceAsString());

        } finally {
            $this->io->success("Saved ".$this->sourcesFlushed." new sources");
        }

        return 0;
    }

    private function skeptic_check(string $entityClass, array $criteria, $condition = true)
    {   
        $entity = null;
        if ($condition) {
            $repo = $this->entityManager->getRepository($entityClass);
            $entity = $repo->findOneBy($criteria);
        }

        if (!$entity) {
            // No existing entity → create one dynamically
            $reflection = new ReflectionClass($entityClass);
            $entity = $reflection->newInstance();
        }
        
        return $entity;
    }

    private function download($downloadPath, $asName, $try = 1) {
        try {
            if ($filePath = $this->pdfManager->download($downloadPath, $asName, $this->client)) {
                if ($this->triedCount < $this->currentProxyIndex) {
                    $this->triedCount = $this->currentProxyIndex;
                    $this->addToTried();
                }
                return $filePath;
            } 
        } catch (Exception $e) {
            $this->io->warning("DOWNLOAD ERROR (Try: $try, IPNumber: ".$this->currentProxyIndex.") : ".$e->getMessage());
            try {
                if ($this->client->getResponse()->getStatusCode() === 200) {
                    if ($this->triedCount < $this->currentProxyIndex) {
                        $this->triedCount = $this->currentProxyIndex;
                        $this->addToTried();
                    }
                }
            } catch (Exception $ee) {

            }
        }

        if ($this->countProxies > $this->currentProxyIndex) {
            if ($try == self::MAX_TRIES) {
                throw new Exception("Tried ". self::MAX_TRIES." times with different proxies and failed.");
            }
        } else {
            if (!$this->demandNewList()) {
                throw new Exception("All proxies has been used.");
            }
        } 
        
        $this->newProxy();
        return $this->download($downloadPath, $asName, $try+1);
    }

    private function getResponse($url, $try = 1) {
        try {
            return $this->client->request('GET', $url);
        } catch (Exception $e) {
            $this->io->warning("NO RESPONSE (Try: $try, IPNumber: ".$this->currentProxyIndex.") : ".$e->getMessage());
        }
        
        if ($this->countProxies > $this->currentProxyIndex) {
            if ($try == self::MAX_TRIES) {
                throw new Exception("Tried ". self::MAX_TRIES." times with different proxies and failed.");
            }
        } else {
            if (!$this->demandNewList()) {
                throw new Exception("All proxies has been used.");
            }
        } 
        
        $this->newProxy();
        return $this->getResponse($url, $try+1);

    }

    private function getProxyList() {
        $workingArray = file($this->proxyListDirectory."/workingProxies.txt", FILE_IGNORE_NEW_LINES);
        $untestedArray = file($this->proxyListDirectory."/proxies.txt", FILE_IGNORE_NEW_LINES);
        $this->proxyArray = array_values(array_unique(array_merge($workingArray, $untestedArray), SORT_STRING));

        $this->triedCount = count($workingArray);
        $this->countProxies = count($this->proxyArray);

        if ($this->countProxies == 0) {
            throw new Exception("No proxy found in list.");
        } else {
            $this->io->success("Found ".$this->countProxies." proxies in list (".$this->triedCount." tested) (".(count($untestedArray) - $this->triedCount)." untested)");
        }

        $this->currentProxyIndex = 1;
    }

    private function newProxy() {
        $this->proxy = $this->proxyArray[($this->currentProxyIndex++)-1];

        $this->client = new Client(HttpClient::create([
            'proxy' => 'http://'.$this->proxy,
            'timeout' => 60
        ]));
    }

    private function demandNewList() {
        if ($this->io->confirm("Continue? If so, please renew the proxy list first.")) {
            $this->getProxyList();
            return true;
        }

        return false;
    }

    private function addToTried() {
        $fp = fopen($this->proxyListDirectory."/workingProxies.txt", "a"); //opens file in append mode  
        fwrite($fp, $this->proxyArray[$this->currentProxyIndex]."\n");
        fclose($fp);

        $this->io->info("Added new proxy ".$this->proxyArray[$this->currentProxyIndex]." to working list.");
    }

    private function getSource($fullName, $params, $retry = false) {
        $filePath = $this->pdfManager->getDirectory()."/".$fullName;

         if (!$retry && $this->pdfManager->check($filePath)) {
            $this->io->comment("Loading saved file");
            
        } else {
            $this->download(self::SOURCE_URL.str_replace("vce-to-pdf", "download", $params["pdfLink"]), $fullName);
            $this->io->comment("Downloaded new source.");
        }

        try {
            list($_import_msg) = PDFImporter::import($this->entityManager, $params["examPaper"], $filePath, $this->io);
            $this->io->comment($_import_msg);

        } catch (Exception $e){
            $this->io->error("IMPORTATION ERROR: ".$e->getMessage());

            // if ($this->io->confirm("Redownload file? If no, then the paper will be skipped.")) {
                
                 $this->pdfManager->delete($filePath);
            //     $this->getSource($params, true);
            // } else {
                $this->entityManager->detach($params["examPaper"]);
            // }
        }
    }
}