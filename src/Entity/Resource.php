<?php

namespace App\Entity;

use App\Repository\ResourceRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
class Resource {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $emitter = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 52)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $restricted = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $expiration = null;


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

    public function getCreated(): ?DateTimeInterface {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): self {
        $this->created = $created;
        return $this;
    }

    public function getExpiration(): ?DateTimeInterface {
        return $this->expiration;
    }

    public function setExpiration(DateTimeInterface $expiration): self {
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
