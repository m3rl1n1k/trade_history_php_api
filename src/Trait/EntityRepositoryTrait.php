<?php

namespace App\Trait;

trait EntityRepositoryTrait
{
    public function getRecordEntityFromUrl(string $url): string|object
    {
        $result = $this->findOneBy(['id' => $this->extractIdFromUrl($url)]);
        if (is_null($result)) {
            return $url;
        }
        return $result;
    }

    protected function extractIdFromUrl(string $url): int
    {
        $data = explode("/", $url);
        return $data[count($data) - 1];
    }
}