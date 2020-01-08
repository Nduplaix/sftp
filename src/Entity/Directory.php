<?php


namespace App\Entity;


use Symfony\Component\DependencyInjection\Container;

class Directory
{
    /**
     * @var Image[]
     */
    private $images;

    /**
     * @return Image[]
     */
    public function getImages(): ?array
    {
        $files = [];
        $dir = scandir(__DIR__. '/../../public/images/');
        foreach ($dir as $file)
        {
            if (!is_dir($file))
            {
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * @param Image[] $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }



}
