<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        )
    ],
    normalizationContext: [
        'groups' => ['tag:read']
    ],
    denormalizationContext: [
        'groups' => ['tag:write']
    ]
)]
class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique:true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['tag:read'])]
    private $id;

    #[ORM\Column(length: 255)]
    #[Groups(['tag:read', 'tag:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'children')]
    #[Groups(['tag:read', 'tag:write'])]
    #[Assert\NotNull]
    private ?Tag $parent = null;

    #[ORM\OneToMany(targetEntity: Tag::class, mappedBy: 'parent')]
    #[Groups(['tag:read', 'tag:write'])]
    private Collection $children;

    #[ORM\Column]
    #[Groups(['tag:read', 'tag:write'])]
    private bool $public = true;

    #[ORM\ManyToMany(targetEntity: Image::class, inversedBy: 'tags')]
    #[Groups(['tag:read', 'tag:write'])]
    private Collection $images;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    public function getParent(): ?Tag
    {
        return $this->parent;
    }

    public function setParent(?Tag $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function addChild(Tag $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Tag $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

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
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
}
