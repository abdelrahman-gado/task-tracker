<?php declare(strict_types=1);

namespace App\Concretes;

use App\Interfaces\TaskManagerInterface;

final readonly class TaskManager implements TaskManagerInterface
{
    /**
     * @inheritDoc
     */
    public function add(string $taskDescription): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $taskId): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     * @return array{id?: string, description?: string, status?: string, created_at?: string, updated_at?: string}[]
     */
    public function list(?string $taskStatus = null): array
    {
        return [
            [
                'id' => '1',
                'description' => 'Sample Task',
                'status' => 'todo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function markDone(int $taskId): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function markInProgress(int $taskId): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function update(int $taskId, string $newTaskDescription): bool
    {
        return false;
    }
}
