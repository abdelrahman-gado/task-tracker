<?php declare(strict_types=1);

namespace App\Interfaces;

interface StorageInterface
{
    /**
     * @param array<int, array{id: int, description: string, status:string, createdAt:string, updatedAt:string}> $items
     */
    public function store(array $items): void;

    /**
     * @return array<int, array{id: int, description: string, status:string, createdAt:string, updatedAt:string}>
     */
    public function load(): array;

    public function getLastId(): int;
}
