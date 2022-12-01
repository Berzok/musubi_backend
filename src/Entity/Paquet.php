<?php

namespace App\Entity;

use App\Repository\PaquetRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaquetRepository::class)]
class Paquet {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $emitter = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 52, unique: true)]
    private ?string $code = null;

    /**
     * Is either the key in the bucket, or the filename if locally uploaded
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private bool $restricted = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $created = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $expiration = null;


    public function getId(): ?int {
        return $this->id;
    }

    public function getEmitter(): ?User {
        return $this->emitter;
    }

    public function setEmitter(User $emitter): self {
        $this->emitter = $emitter;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(string $code): self {
        $this->code = $code;
        return $this;
    }

    public function getCreated(): ?DateTimeImmutable {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created): self {
        $this->created = $created;
        return $this;
    }

    public function getExpiration(): ?DateTimeImmutable {
        return $this->expiration;
    }

    public function setExpiration(DateTimeImmutable $expiration): self {
        $this->expiration = $expiration;
        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function setSlug(string $slug): self {
        $this->slug = $slug;
        return $this;
    }

    public function isRestricted(): ?bool {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted): self {
        $this->restricted = $restricted;
        return $this;
    }
}
