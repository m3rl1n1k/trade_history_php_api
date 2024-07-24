<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use stdClass;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'setting', cascade: ['persist', 'remove'])]
    private User $user;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $setting = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSetting(): null|string|stdClass
    {
        return json_decode($this->setting);
    }

    public function setSetting(?string $setting): static
    {
        if ($setting === null) {
            $setting = $this->setDefaultSetting();
        }
        $this->setting = $setting;

        return $this;
    }

    private function setDefaultSetting(): string
    {
        $setting = [
            "category" => [
                'categoriesWithoutColor' => false,
                'colored_categories' => true,
                'colored_main_category' => true,
                'default_color_to_main_category' => "#1c6263",
                'default_color_to_category' => "#1c6263",
            ],
            "chart" => [
                'color_expense_chart' => "#eeeeee",
                'color_income_chart' => "#ffffff",
            ],
            "pagination" => [
                'transactions_per_page' => 20
            ],
        ];
        return json_encode($setting, JSON_PRETTY_PRINT);
    }
}
