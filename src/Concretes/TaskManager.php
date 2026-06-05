<?php declare(strict_types=1);

namespace App\Concretes;

use App\Entities\Task;
use App\Enums\TaskStatusEnum;
use App\Interfaces\TaskManagerInterface;

final readonly class TaskManager implements TaskManagerInterface
{
    /**
     * @inheritDoc
     */
    public function add(string $taskDescription): ?Task
    {
        return new Task(id: 1, description: $taskDescription);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $taskId): ?Task
    {
        return new Task(id: $taskId, description: 'Deleted Task');
    }

    /**
     * @inheritDoc
     * @return Task[]
     */
    public function list(?string $taskStatus = null): array
    {
        return [
            new Task(id: 1, description: 'Sample Task 1', status: TaskStatusEnum::TODO),
            new Task(id: 2, description: 'Sample Task 2', status: TaskStatusEnum::IN_PROGRESS),
            new Task(id: 3, description: 'Sample Task 3', status: TaskStatusEnum::DONE),
        ];
    }

    /**
     * @inheritDoc
     */
    public function markDone(int $taskId): ?Task
    {
        return new Task(id: $taskId, description: 'Marked Done Task', status: TaskStatusEnum::DONE);
    }

    /**
     * @inheritDoc
     */
    public function markInProgress(int $taskId): ?Task
    {
        return new Task(id: $taskId, description: 'Marked In Progress Task', status: TaskStatusEnum::IN_PROGRESS);
    }

    /**
     * @inheritDoc
     */
    public function update(int $taskId, string $newTaskDescription): ?Task
    {
        return new Task(id: $taskId, description: $newTaskDescription);
    }
}
