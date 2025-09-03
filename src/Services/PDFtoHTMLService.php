<?php 
// src/Service/PDFtoHTMLService.php
namespace App\Services;

use Exception;
use Gufy\PdfToHtml\Pdf;

class PDFtoHTMLService
{
    private $documentsDirectory;
    private $filePath = "imports/GratisExam.AACD.Braindumps.AACD.v2015-03-19.by.Robert.100q.pdf";

    public function __construct($docsDirectory, $kernelDirectory) {
        $this->documentsDirectory = $docsDirectory;

        \Gufy\PdfToHtml\Config::getInstance()->set('pdftohtml.bin', $kernelDirectory."/vendor/bin/poppler/pdftohtml.exe");
        \Gufy\PdfToHtml\Config::getInstance()->set('pdfinfo.bin', $kernelDirectory."/vendor/bin/poppler/pdfinfo.exe");

    }

    public function extract($io) {
        $fullPath = $this->documentsDirectory."/$this->filePath";
        try {
            // Parse PDF file and build necessary objects.
            $config = new \Smalot\PdfParser\Config();
            $config->setFontSpaceLimit(-200);
            $parser = new \Smalot\PdfParser\Parser([], $config);

            $pdf = $parser->parseFile($fullPath);

            ini_set('memory_limit', '-1');
            file_put_contents($this->documentsDirectory."/dict.txt", json_encode($pdf->getDictionary()));
        } catch (Exception $e) {
            
            $io->error($e->getLine()." (".$e->getFile().") - ".$e->getMessage());
            $io->error($e->getTraceAsString());
        }

    }
}