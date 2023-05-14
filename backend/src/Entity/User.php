<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model;
use App\Controller\LoginController;
use App\Repository\UserRepository;
use App\State\UserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "object == user or is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            processor: UserProcessor::class,
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            processor: UserProcessor::class,
            security: "object == user or is_granted('ROLE_ADMIN')",
            securityPostDenormalize: "object.isAdmin() == previous_object.isAdmin() or is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "object == user or is_granted('ROLE_ADMIN')"
        ),
        new Post(
            name: 'api_login',
            uriTemplate: '/login',
            controller: LoginController::class,
            normalizationContext: [
                'groups' => ['user:read']
            ],
            denormalizationContext: [
                'groups' => ['user:login']
            ],
            read: false,
            write: false,
            deserialize: false,
            status: 204,
            openapi: new Model\Operation(
                summary: "Login to API.",
                description: "Login to API with user name and password.",
                requestBody: new Model\RequestBody(
                    description: "User credentials",
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string'],
                                    'password' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]),
                    required: true
                ),
                responses: [
                    '204' => new Model\Response('Login successful'),
                    '401' => new Model\Response('Invalid credentials')
                ]
            )
        ),
        new Get(
            uriTemplate: '/currentUser',
            controller: LoginController::class,
            normalizationContext: [
                'groups' => ['user:read']
            ],
            read: false,
            openapi: new Model\Operation(
                summary: "Get information for current user.",
                description: "Get information for currently logged in user. Returns with 404 if no user is logged in.",
                responses: [
                    '200' => new Model\Response('Currently logged in user.'),
                    '404' => new Model\Response('No user logged in.')
                ]
            )
        ),
        new Get(
            description: "Logout from API",
            name: 'api_logout',
            uriTemplate: '/logout',
            openapi: new Model\Operation(
                summary: "Logout from API.",
                description: "Logs out from API."
            )
        )
    ],
    normalizationContext: [
        'groups' => ['user:read']
    ],
    denormalizationContext: [
        'groups' => ['user:write']
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique:true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read'])]
    private $id;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(['user:read', 'user:write', 'user:login'])]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:write', 'user:login'])]
    #[SerializedName('password')]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $fullname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private ?bool $admin = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

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
            $image->setAuthor($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAuthor() === $this) {
                $image->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
