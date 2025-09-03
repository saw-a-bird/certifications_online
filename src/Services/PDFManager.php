<?php 
// src/Service/FileDowloader.php
namespace App\Services;

use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PDFManager {
    
    private $documentsDirectory;
    const IMPORTS_DIR = "imports", SUGGESTIONS_DIR = "suggestions";

    public function __construct($docsDirectory) {
        $this->documentsDirectory = $docsDirectory;
    }

    public function download($url, $asName = null, \Goutte\Client $client = null) {
        try {
            // Use basename() function to return the base name of file
            $filePath = self::IMPORTS_DIR."/".($asName ?: md5(uniqid()).'.'.pathinfo(basename($url)));
            $fullPath = $this->documentsDirectory."/".$filePath;

            if ($client == null) {
                file_put_contents($fullPath, file_get_contents($url));
            } else {
                $crawler = $client->request('GET', $url);
                $pdfContent = $client->getResponse()->getContent();
                file_put_contents($fullPath, $pdfContent);
            }

            return $filePath;
        } catch (Exception $e) {
            throw $e;
        }
    }

    
    public function upload(UploadedFile $file, $isSuggestion) {
        // the file is initially named as smth.tmp (just temporary file) so we have change it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getDirectory($isSuggestion), $fileName);

        return  $file->getRealPath();
    }

    public function moveToMain($filePath) {
        $file = new File($this->documentsDirectory."/".$filePath);
        $file->move($this->getDirectory(false));

        return $file->getRealPath();
    }

    public function delete($fullPath) {
        if(file_exists($fullPath)) {
            unlink($fullPath);
        } 
    }

    public function check($fullPath) {
        if(file_exists($fullPath)) {
            return true;
        } 

        return false;
    }

    public function getDirectory(?bool $IsSuggestion = false) {
        if ($IsSuggestion == false) {
            return $this->documentsDirectory."/".self::IMPORTS_DIR;
        } else {
            return $this->documentsDirectory."/".self::SUGGESTIONS_DIR;
        }
    }
}