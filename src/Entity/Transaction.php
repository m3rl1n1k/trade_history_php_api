<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use stdClass;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    const INCOME = "income";
    const EXPENSE = "expense";
    const TRANSFER = "transfer";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?int $id = null;

    #[ORM\Column(type: "float")]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?float $amount = null;

    #[ORM\Column]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?string $type = null;

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
    #[ORM\JoinColumn(onDelete: 'set null')]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountCurrent(): ?float
    {
        return $this->amount;
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
    public function setDataForTransaction(Transaction $transaction, stdClass $data): Transaction
    {
        $category = !is_string($data->category) ? $data->category : null;
        $create_at = $data->created_at . $this->getCurrentTime();
        if ($data->flag === 'new')
        {
            match ($data->type){
                "income" => $transaction->setIncome(),
                "expense" => $transaction->setExpense(),
                default => $transaction->setTypeDefault(),
            };
        }

        $transaction->setAmount($data->amount);
        $transaction->setDescription($data->description);
        $transaction->setCreatedAt(new DateTimeImmutable($create_at));
        $transaction->setCategory($category);
        $transaction->setWallet($data->wallet);
        return $transaction;
    }

    public function setTypeDefault(): static
    {
        $this->type = self::EXPENSE;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isExpense(): bool
    {
        return $this->getType() === self::EXPENSE;
    }

    public function isTransfer(): bool
    {
        return $this->getType() === self::TRANSFER;
    }

    private function getCurrentTime(): string
    {
        $date = new DateTimeImmutable();
        return date_format($date, 'H:i:s');

    }

}
