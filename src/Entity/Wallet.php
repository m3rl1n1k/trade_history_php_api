<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{

    const CARD_NUMBER_LENGTH = 8;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wallet:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    #[Groups(['wallet:read'])]
    private ?string $number = null;

    #[ORM\Column(length: 10)]
    #[Groups(['wallet:read'])]
    private ?string $currency = null;

    #[ORM\Column(type: "bigint", nullable: true)]
    #[Groups(['wallet:read'])]
    private ?int $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['wallet:read'])]
    private ?string $card_name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string|int $currency): static
    {
        $number = null;
        for ($i = 1; $i <= self::CARD_NUMBER_LENGTH; $i++) {
            $number .= mt_rand(0, 9);
        }
        $this->number = $currency . $number;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount / 100;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount * 100;

        return $this;
    }

    public function getAmountCurrent(): ?float
    {
        return $this->amount;
    }

    public function setAmountCurrent(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCardName(): ?string
    {
        return $this->card_name;
    }

    public function setCardName(?string $card_name): static
    {
        $this->card_name = $card_name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function increment($amount): int
    {
        return $this->amount += $amount;
    }

    public function decrement($amount): int
    {
        return $this->amount -= $amount;
    }
}
