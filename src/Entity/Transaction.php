<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    const INCOME = 1;
    const EXPENSE = 2;
    const TRANSFER = 3;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?int $amount = null;

    #[ORM\Column]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?int $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['transaction:read'])]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transaction:read', 'wallet:read'])]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transaction:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount / 100;
    }

    public function setAmount(int|float $amount): static
    {
        $this->amount = is_int($amount) ? $amount : $amount * 100;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): static
    {
        $this->wallet = $wallet;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setDataForTransaction(Transaction $transaction, array $data): Transaction
    {
        $transaction->setAmount($data['amount']);
        $transaction->setType();
        $transaction->setDescription($data['description']);
        $transaction->setCreatedAt(new DateTimeImmutable($data['created_at']));
        $transaction->setCategory($data['category']);
        $transaction->setWallet($data['wallet']);
        return $transaction;
    }

    public function setIncome(): Transaction
    {
        $this->type = self::INCOME;
        return $this;
    }

    public function setExpense(): Transaction
    {
        $this->type = self::EXPENSE;
        return $this;
    }

    public function setTransfer(): Transaction
    {
        $this->type = self::TRANSFER;
        return $this;
    }

    public function isIncome(): bool
    {
        return $this->getType() === self::INCOME;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(): static
    {
        $this->type = self::EXPENSE;

        return $this;
    }

    public function isExpense(): bool
    {
        return $this->getType() === self::INCOME;
    }

    public function isTransfer(): bool
    {
        return $this->getType() === self::INCOME;
    }
}
