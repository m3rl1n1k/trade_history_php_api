<?php

namespace App\Service;

use App\Repository\SettingRepository;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;

class SettingService
{
    private stdClass|string|null $settings;

    public function __construct(protected SettingRepository $settingRepository, protected Security $security)
    {

        $this->settings = $this->getSettings();

    }

    protected function getSettings(): string|stdClass|null
    {
        $userId = $this->security->getUser()->getId();
        $settings = $this->settingRepository->findOneBy(['user' => $userId]);
        if ($settings)
            return $settings->getSetting();
        return null;
    }

    public function currencyDefaultUser(): ?string
    {
        return $this->settings->currency->currency_default;
    }

    public function currencySelected(): ?int
    {
        return $this->settings->pagination->currency_selected;
    }

    public function categoriesWithoutColor(): ?string
    {
        return $this->settings->category->categories_without_color;
    }

    public function categoryColor(): ?string
    {
        return $this->settings->category->colored_categories;
    }

    public function categoryMainColor(): ?string
    {
        return $this->settings->category->colored_main_category;
    }

    public function categoryMainColorDefault(): ?string
    {
        return $this->settings->category->default_color_to_main_category;
    }

    public function categoryColorDefault(): ?string
    {
        return $this->settings->category->default_color_to_category;
    }

    public function chartColorExpense(): ?string
    {
        return $this->settings->category->color_expense_chart;
    }

    public function chartColorIncome(): ?string
    {
        return $this->settings->category->color_income_chart;
    }

    public function paginationTransactionPerPage(): ?int
    {
        return $this->settings->pagination->transactions_per_page;
    }


}