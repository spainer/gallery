<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface;

use App\Repository\AlbumRepository;
use App\Repository\ImageRepository;

#[AsController]
class ImageUploadController extends AbstractController
{
    public function __construct(
        private AlbumRepository $albumRepository,
        private ImageRepository $imageRepository
    ) {}

    public function __invoke(Uuid $id, Request $request, LoggerInterface $log): Image
    {
        $album = $this->albumRepository->find($id);

        $file = $request->files->get('file');
        $extension = $file->guessExtension();
        if ($extension === null) {
            $log->debug('Could not estimate extension, trying to use user MIME type.');
            $extension = $file->guessClientExtension();
            if ($extension === null) {
                $log->warning('Could not estimate extension, using NULL');
            }
        }

        $image = new Image();
        $image->setAlbum($album);
        $image->setType($extension);
        $this->imageRepository->save($image, true);

        $filename = $image->getId();
        if ($extension !== null) {
            $filename .= '.'.$extension;
        }

        $log->debug('Moving uploaded file to '.$filename);
        $file->move($this->getParameter('gallery.directories.original_images'), $filename);

        return $image;
    }
}
