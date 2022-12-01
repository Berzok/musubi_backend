<?php

namespace App\Service;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader {

    private SluggerInterface $slugger;
    private string $uploadDirectory;

    /**
     * @param SluggerInterface $slugger
     * @param string $uploadDirectory
     */
    public function __construct(SluggerInterface $slugger, string $uploadDirectory) {
        $this->slugger = $slugger;
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file): string {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $file->move(
                $this->uploadDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            dd($e);
            exit();
            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }
}