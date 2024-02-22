<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: '`character`')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getAllCharacters'])]
    private ?int $id = null;

    #[ORM\Column(length: 24)]
    #[Groups(['getAllCharacters'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAllCharacters'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAllCharacters'])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'main', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'main', targetEntity: Combo::class)]
    private Collection $combos;

    #[ORM\OneToMany(mappedBy: 'main', targetEntity: ProPlayer::class)]
    private Collection $proPlayers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->combos = new ArrayCollection();
        $this->proPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setMain($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getMain() === $this) {
                $user->setMain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Combo>
     */
    public function getCombos(): Collection
    {
        return $this->combos;
    }

    public function addCombo(Combo $combo): static
    {
        if (!$this->combos->contains($combo)) {
            $this->combos->add($combo);
            $combo->setMain($this);
        }

        return $this;
    }

    public function removeCombo(Combo $combo): static
    {
        if ($this->combos->removeElement($combo)) {
            // set the owning side to null (unless already changed)
            if ($combo->getMain() === $this) {
                $combo->setMain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProPlayer>
     */
    public function getProPlayers(): Collection
    {
        return $this->proPlayers;
    }

    public function addProPlayer(ProPlayer $proPlayer): static
    {
        if (!$this->proPlayers->contains($proPlayer)) {
            $this->proPlayers->add($proPlayer);
            $proPlayer->setMain($this);
        }

        return $this;
    }

    public function removeProPlayer(ProPlayer $proPlayer): static
    {
        if ($this->proPlayers->removeElement($proPlayer)) {
            // set the owning side to null (unless already changed)
            if ($proPlayer->getMain() === $this) {
                $proPlayer->setMain(null);
            }
        }

        return $this;
    }
}
