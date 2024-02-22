<?php

namespace App\Entity;

use App\Repository\PayloadTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayloadTypeRepository::class)]
class PayloadType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 24)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'payloadType', targetEntity: Payload::class)]
    private Collection $payloads;

    public function __construct()
    {
        $this->payloads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Payload>
     */
    public function getPayloads(): Collection
    {
        return $this->payloads;
    }

    public function addPayload(Payload $payload): static
    {
        if (!$this->payloads->contains($payload)) {
            $this->payloads->add($payload);
            $payload->setPayloadType($this);
        }

        return $this;
    }

    public function removePayload(Payload $payload): static
    {
        if ($this->payloads->removeElement($payload)) {
            // set the owning side to null (unless already changed)
            if ($payload->getPayloadType() === $this) {
                $payload->setPayloadType(null);
            }
        }

        return $this;
    }
}
