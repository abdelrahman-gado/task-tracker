<?php declare(strict_types=1);

namespace App\Concretes;

use App\Interfaces\StorageInterface;
use Exception;

final class JsonFileStorageHandler implements StorageInterface
{
    private int $lastId = 0;

    public function __construct(private readonly string $filePath)
    {
        if (!file_exists($this->filePath)) {
            $created = file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT));
            if ($created === false) {
                throw new Exception("Error: can't create file");
            }
        }
    }

    /**
     * @inheritDoc
     * @return array<int, array{id: int, description: string, status:string, createdAt:string, updatedAt:string}>
     */
    public function load(): array
    {
        $fileContent = file_get_contents($this->filePath);
        if ($fileContent === false) {
            throw new Exception("Error: can't read file content");
        }

        /**
         * @var array<int, array{id: int, description: string, status:string, createdAt:string, updatedAt:string}>|null $items
         */
        $items = json_decode($fileContent, true);
        if ($items === null) {
            throw new Exception("Error: can't load file");
        }

        $this->lastId = (int) array_key_last($items);
        return $items;
    }

    /**
     * @inheritDoc
     * @param array<int, array{id: int, description: string, status:string, createdAt:string, updatedAt:string}> $items
     */
    public function store(array $items): void
    {
        $created = file_put_contents($this->filePath, json_encode($items, JSON_PRETTY_PRINT));
        if ($created === false) {
            throw new Exception("Error: can't store an object to file");
        }
    }

    public function getLastId(): int
    {
        return $this->lastId;
    }
}
