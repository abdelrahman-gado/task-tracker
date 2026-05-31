<?php declare(strict_types=1);

namespace App\Interfaces;

interface TaskManagerInterface
{
    public function add(string $taskDescription): bool;

    /**
     * @return array{id?: string, description?: string, status?: string, created_at?: string, updated_at?: string}[]
     */
    public function list(?string $taskStatus = null): array;

    public function update(int $taskId, string $newTaskDescription): bool;

    public function delete(int $taskId): bool;

    public function markDone(int $taskId): bool;

    public function markInProgress(int $taskId): bool;
}
