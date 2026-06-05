<?php declare(strict_types=1);

namespace App\Entities;

use App\Enums\TaskStatusEnum;
use App\Interfaces\Arrayable;
use DateTimeImmutable;

final class Task implements Arrayable, \Stringable
{
    public function __construct(
        public readonly int $id,
        public string $description,
        public TaskStatusEnum $status = TaskStatusEnum::TODO,
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
        public DateTimeImmutable $updatedAt = new DateTimeImmutable(),
    ) {}

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return sprintf(
            'Task #%d: %s [%s] (created at: %s, updated at: %s)',
            $this->id,
            $this->description,
            $this->status->value,
            $this->createdAt->format(DateTimeImmutable::ATOM),
            $this->updatedAt->format(DateTimeImmutable::ATOM)
        );
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $arr): static
    {
        return new self(
            id: $arr['id'],
            description: $arr['description'],
            status: TaskStatusEnum::from($arr['status']),
            createdAt: new DateTimeImmutable($arr['createdAt']),
            updatedAt: new DateTimeImmutable($arr['updatedAt']),
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format(DateTimeImmutable::ATOM),
            'updatedAt' => $this->updatedAt->format(DateTimeImmutable::ATOM),
        ];
    }
}
