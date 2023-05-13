<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

use App\Controller\ImageUploadController;
use App\State\ImageProcessor;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Put(),
        new Patch(),
        new Delete(processor: ImageProcessor::class)
    ]
)]
#[ApiResource(
    uriTemplate: '/albums/{id}/images',
    uriVariables: [
        'id' => new Link(
            fromClass: Album::class,
            fromProperty: 'images'
        )
        ],
        operations: [
            new GetCollection(),
            new Post(
                controller: ImageUploadController::class,
                read: false,
                deserialize: false
            )
        ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique:true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private $id;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $imageDate = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: ExifData::class, orphanRemoval: true)]
    private Collection $exifData;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'images')]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->exifData = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImageDate(): ?\DateTimeInterface
    {
        return $this->imageDate;
    }

    public function setImageDate(\DateTimeInterface $imageDate): self
    {
        $this->imageDate = $imageDate;

        return $this;
    }

    /**
     * @return Collection<int, ExifData>
     */
    public function getExifData(): Collection
    {
        return $this->exifData;
    }

    public function addExifData(ExifData $exifData): self
    {
        if (!$this->exifData->contains($exifData)) {
            $this->exifData->add($exifData);
            $exifData->setImage($this);
        }

        return $this;
    }

    public function removeExifData(ExifData $exifData): self
    {
        if ($this->exifData->removeElement($exifData)) {
            // set the owning side to null (unless already changed)
            if ($exifData->getImage() === $this) {
                $exifData->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addImage($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeImage($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
