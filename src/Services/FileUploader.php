<?php 
// src/Service/FileUploader.php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $imagesDirectory;

    public function __construct($imgsDirectory) {
        $this->imagesDirectory = $imgsDirectory;
    }

    public function upload(UploadedFile $file, $type) {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory($type), $fileName);
        return $fileName;
    }

    public function getTargetDirectory($type) {
        if ($type == "avatar") {
            return $this->imagesDirectory."/avatars";
        } elseif ($type == "thumbnail_provider") {
            return $this->imagesDirectory."/thumbnails/providers";
        } elseif ($type == "thumbnail_certif") {
            return $this->imagesDirectory."/thumbnails/certifications";
        }
    }
}