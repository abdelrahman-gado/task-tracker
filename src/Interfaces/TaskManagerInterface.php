<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\Task;

interface TaskManagerInterface
{
    public function add(string $taskDescription): Task;

    /**
     * @return Task[]
     */
    public function list(?string $taskStatus = null): array;

    public function update(int $taskId, string $newTaskDescription): ?Task;

    public function delete(int $taskId): ?Task;

    public function markDone(int $taskId): ?Task;

    public function markInProgress(int $taskId): ?Task;
}
