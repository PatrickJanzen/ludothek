<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(shortName: 'Meeple')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: 'true')]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[ApiProperty(readable: false)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $memberSince = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'holder', targetEntity: Game::class)]
    #[Link(toProperty: 'holder')]
    private Collection $games;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Game::class, orphanRemoval: true)]
    #[Link(toProperty: 'owner')]
    private Collection $myGames;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->myGames = new ArrayCollection();
    }

    public function getId(): string
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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMemberSince(): ?\DateTimeImmutable
    {
        return $this->memberSince;
    }

    public function setMemberSince(\DateTimeImmutable $memberSince): self
    {
        $this->memberSince = $memberSince;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
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

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setHolder($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getHolder() === $this) {
                $game->setHolder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getMyGames(): Collection
    {
        return $this->myGames;
    }

    public function addMyGame(Game $myGame): self
    {
        if (!$this->myGames->contains($myGame)) {
            $this->myGames->add($myGame);
            $myGame->setOwner($this);
        }

        return $this;
    }

    public function removeMyGame(Game $myGame): self
    {
        if ($this->myGames->removeElement($myGame)) {
            // set the owning side to null (unless already changed)
            if ($myGame->getOwner() === $this) {
                $myGame->setOwner(null);
            }
        }

        return $this;
    }
}
