<?php

namespace App\Service;

use App\Entity\Image;

class ImageService
{
    public function __construct(
        private string $originalImageDirectory
    ) {}

    public function deleteImage(Image $image) {
        unlink($this->getOriginalImagePath($image));
    }

    public function getOriginalImagePath(Image $image): string {
        $filename = $image->getId();
        if ($image->getType() !== null) {
            $filename .= '.'.$image->getType();
        }

        return $this->originalImageDirectory.'/'.$filename;
    }
}
