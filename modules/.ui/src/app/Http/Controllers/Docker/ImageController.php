<?php

namespace App\Http\Controllers\Docker;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends DockerController
{
    public function imagesPage()
    {
        return view('images', [ 'images' => $this->getImages() ]);
    }

    public function getImages()
    {
        $imageManager = $this->docker->getImageManager();

        $images = array();
        foreach ($imageManager->findAll() as $image) {
            $images[] = array(
                'id' => $image->getId(),
                'tags' => $image->getRepoTags(),
                'size' => $image->getSize()
            );
        }

        $images = json_encode($images);
        
        return json_decode($images);
    }

    public function pullImage()
    {
        
    }
}
