<?php 
// src/Service/FileUploader.php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wbrframe\PdfToHtml\Converter\ConverterFactory;
use App\Entity\Questions;
use App\Entity\Propositions;
use App\Entity\Exams;

class PDFImporter
{
    private $documentsDirectory;

    public function __construct($documentsDirectory) {
        $this->documentsDirectory = $documentsDirectory;
    }
    
    public function import(Exams $exam, UploadedFile $file) {
        $questions = array();

        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        // Parse PDF file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($this->getTargetDirectory()."\\$fileName");
        $text = $pdf->getText();

        $questionArray = preg_split("/QUESTION \d*/", $text, -1);
        array_splice($questionArray, 0, 1);
        
        $counter = 0;
        $remove = false;
        foreach ($questionArray as $kQ => $questionFull) {
            $question = new Questions();
            $question->setTitle("QUESTION ".(++$counter));
            $question->setExam($exam);

            $propositions =  preg_split("/^\s*\w\./m", $questionFull, -1);

            if (count($propositions) > 1) {
                $question->setTask($propositions[0]);
                array_splice($propositions, 0, 1);  // remove task 

                $last_answer = $propositions[count($propositions)-1];
    
                preg_match("/^.*$/m", $last_answer, $getLast); // get last
                array_splice($propositions, count($propositions)-1, 1, $getLast);  // put last 
                
                preg_match("/Correct Answer:\s*(?P<word>\w+)$/m", $last_answer, $correct_answers_full);
                
                $correct_answers = array();
                if (isset($correct_answers_full["word"])) {
                    $word = $correct_answers_full["word"];
                    
                    $strlen = strlen($word);
                    for($i = 0; $i < $strlen; ++$i) {
                        $correct_answers[$this->toNum($word[$i])] = true;
                    }
                }
    
                foreach ($propositions as $kA => $propositionFull) {
                    $propositionFull = substr_replace($propositionFull, "", -1);
    
                    if (strlen($propositionFull) != 0) {
                        $proposition = new Propositions();
                        $proposition->setProposition($propositionFull);
                        $question->addProposition($proposition);
    
                        if (isset($correct_answers[$kA])) {
                            $proposition->setisCorrect(true);
                        }
                    } else if ($kA == 0) {
                        $counter--;
                        $remove = true;
                    }
                }
            } else {
                $remove = true;
            }
            
            
            if ($remove == false) {
                array_push($questions, $question);
            } else {
                $remove = false;
            }
        }

        return $questions;
    }

    function toNum($data) {
        $alphabet = array( 
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y',
            'Z');

        $alpha_flip = array_flip($alphabet);
        $return_value = -1;
        $length = strlen($data);
        for ($i = 0; $i < $length; $i++) {
            $return_value +=
                ($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
        }
        return $return_value;
    }

    public function getTargetDirectory() {
        return $this->documentsDirectory;
    }
}