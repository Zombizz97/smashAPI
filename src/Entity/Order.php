<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getAllOrder'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['getAllOrder'])]
    private ?int $sequence = null;

    #[ORM\Column(length: 24)]
    #[Groups(['getAllOrder'])]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Character $main = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMain(): ?Character
    {
        return $this->main;
    }

    public function setMain(?Character $main): static
    {
        $this->main = $main;

        return $this;
    }

    #[Groups(['getAllOrder'])]
    public function getCharacterName(): ?string
    {
        return $this->main ? $this->main->getName() : null;
    }
}
