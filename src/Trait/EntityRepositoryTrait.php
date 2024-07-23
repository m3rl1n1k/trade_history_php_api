<?php

namespace App\Trait;

trait EntityRepositoryTrait
{
    public function getRecordEntityFromUrl(string $url): object
    {
        return $this->findOneBy(['id' => $this->extractIdFromUrl($url)]);
    }

    protected function extractIdFromUrl(string $url): int
    {
        $data = explode("/", $url);
        return $data[count($data) - 1];
    }
}