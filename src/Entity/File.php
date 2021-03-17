<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez choisir un fichier !")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=BeerFile::class, inversedBy="files")
     */
    private $beerFile;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
    */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));    
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getBeerFile(): ?BeerFile
    {
        return $this->beerFile;
    }

    public function setBeerFile(?BeerFile $beerFile): self
    {
        $this->beerFile = $beerFile;

        return $this;
    }
}
