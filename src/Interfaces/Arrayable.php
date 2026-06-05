<?php declare(strict_types=1);

namespace App\Interfaces;

interface Arrayable
{
    /**
     * @return array{id: int, description: string, status:string, createdAt:string, updatedAt:string}
     */
    public function toArray(): array;

    /**
     * @param array{id: int, description: string, status:string, createdAt:string, updatedAt:string} $arr
     */
    public static function fromArray(array $arr): static;
}
