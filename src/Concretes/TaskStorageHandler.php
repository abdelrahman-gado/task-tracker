<?php declare(strict_types=1);

namespace App\Concretes;

use App\Entities\Task;
use App\Enums\TaskStatusEnum;
use App\Interfaces\StorageInterface;
use DateTimeImmutable;

final readonly class TaskStorageHandler
{
    public function __construct(private StorageInterface $storage) {}

    /**
     * @return array<int, Task>
     */
    public function load(): array
    {
        $items = $this->storage->load();
        return array_map(Task::fromArray(...), $items);
    }

    /**
     * @param array<int, Task> $tasks
     */
    public function store(array $tasks): void
    {
        $items = array_map(static fn (Task $task): array => $task->toArray(), $tasks);
        $this->storage->store($items);
    }

    public function getLastId(): int
    {
        return $this->storage->getLastId();
    }

    public function insert(string $taskDescription): Task
    {
        $tasks = $this->load();
        $lastId = $this->getLastId();
        $task = new Task(++$lastId, $taskDescription);
        $tasks[$task->id] = $task;
        $this->store($tasks);
        return $task;
    }

    public function delete(int $taskId): ?Task
    {
        $tasks = $this->load();
        if (!array_key_exists($taskId, $tasks)) {
            return null;
        }

        $task = $tasks[$taskId];
        unset($tasks[$taskId]);
        $this->store($tasks);
        return $task;
    }

    /**
     * @return Task[]
     */
    public function list(?string $taskStatus): array
    {
        $tasks = $this->load();
        if (!$taskStatus) {
            return $tasks;
        }

        return array_filter($tasks, static fn (Task $task): bool => $task->status->value === $taskStatus);
    }

    /**
     * @param array<string, string|TaskStatusEnum> $data
     */
    public function update(int $taskId, array $data): ?Task
    {
        $tasks = $this->load();
        if (!array_key_exists($taskId, $tasks)) {
            return null;
        }

        $taskToUpdate = $tasks[$taskId];
        foreach ($data as $property => $value) {
            if (!property_exists($taskToUpdate, $property)) {
                continue;
            }

            $taskToUpdate->{$property} = $value;
        }

        $taskToUpdate->updatedAt = new DateTimeImmutable();
        $tasks[$taskId] = $taskToUpdate;
        $this->store($tasks);
        return $taskToUpdate;
    }
}
