<?php 
// src/Service/FileUploader.php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $avatarDirectory;
    private $thumbnailDirectory;

    public function __construct($avatarDirectory, $thumbnailDirectory) {
        $this->avatarDirectory = $avatarDirectory;
        $this->thumbnailDirectory = $thumbnailDirectory;
    }

    public function upload(UploadedFile $file, $type) {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory($type), $fileName);

        return $fileName;
    }

    public function getTargetDirectory($type) {
        if ($type == "avatar") {
            return $this->avatarDirectory;
        } elseif ($type == "thumbnail") {
            return $this->thumbnailDirectory;
        }
    }
}