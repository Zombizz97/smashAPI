<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $sequence = null;

    #[ORM\ManyToMany(targetEntity: Payload::class, inversedBy: 'orders')]
    private Collection $payload;

    public function __construct()
    {
        $this->payload = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): static
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return Collection<int, Payload>
     */
    public function getPayload(): Collection
    {
        return $this->payload;
    }

    public function addPayload(Payload $payload): static
    {
        if (!$this->payload->contains($payload)) {
            $this->payload->add($payload);
        }

        return $this;
    }

    public function removePayload(Payload $payload): static
    {
        $this->payload->removeElement($payload);

        return $this;
    }
}
