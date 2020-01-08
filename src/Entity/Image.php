<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Image
{
    /**
     * @Assert\NotBlank(message="L'image doit etre renseigné")
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}
