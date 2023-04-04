<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: ['code'])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: 'true')]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?User $holder = null;

    #[ORM\ManyToOne(inversedBy: 'myGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(unique: true)]
    private ?int $inventoryNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $playerMin = null;

    #[ORM\Column(nullable: true)]
    private ?int $playerMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $durationMinuntesMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $value = null;

    #[ORM\ManyToMany(targetEntity: Publisher::class, inversedBy: 'games')]
    private Collection $publisher;

    public function __construct()
    {
        $this->publisher = new ArrayCollection();
    }

    public function getId(): string
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

    public function getHolder(): ?User
    {
        return $this->holder;
    }

    public function setHolder(?User $holder): self
    {
        $this->holder = $holder;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getInventoryNumber(): ?int
    {
        return $this->inventoryNumber;
    }

    public function setInventoryNumber(int $inventoryNumber): self
    {
        $this->inventoryNumber = $inventoryNumber;

        return $this;
    }

    public function getPlayerMin(): ?int
    {
        return $this->playerMin;
    }

    public function setPlayerMin(?int $playerMin): self
    {
        $this->playerMin = $playerMin;

        return $this;
    }

    public function getPlayerMax(): ?int
    {
        return $this->playerMax;
    }

    public function setPlayerMax(?int $playerMax): self
    {
        $this->playerMax = $playerMax;

        return $this;
    }

    public function getDurationMinuntesMin(): ?int
    {
        return $this->durationMinuntesMin;
    }

    public function setDurationMinuntesMin(?int $durationMinuntesMin): self
    {
        $this->durationMinuntesMin = $durationMinuntesMin;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, Publisher>
     */
    public function getPublisher(): Collection
    {
        return $this->publisher;
    }

    public function addPublisher(Publisher $publisher): self
    {
        if (!$this->publisher->contains($publisher)) {
            $this->publisher->add($publisher);
        }

        return $this;
    }

    public function removePublisher(Publisher $publisher): self
    {
        $this->publisher->removeElement($publisher);

        return $this;
    }
}
