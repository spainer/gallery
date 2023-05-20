<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['album:read', 'album:item:get']
            ]
        ),
        new GetCollection(),
        new Post(
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            uriTemplate: '/albumBySlug/{slug}',
            uriVariables: [
                'slug'
            ],
            normalizationContext: [
                'groups' => ['album:read', 'album:item:get']
            ]
        )
    ],
    normalizationContext: [
        'groups' => ['album:read']
    ],
    denormalizationContext: [
        'groups' => ['album:write']
    ]
)]
class Album
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['album:read'])]
    private $id;

    #[ORM\Column(length: 255)]
    #[Groups(['album:read', 'album:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups(['album:read', 'album:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Image::class, orphanRemoval: true)]
    #[Groups(['album:item:get'])]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAlbum($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAlbum() === $this) {
                $image->setAlbum(null);
            }
        }

        return $this;
    }

    #[Groups(['album:read'])]
    public function getCountImages(): int {
        return $this->images->count();
    }

    #[Groups(['album:read'])]
    public function getPreviewImage(): ?Image {
        return $this->images->count() > 0 ? $this->images->get(0) : null;
    }
}
