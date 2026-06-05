<?php declare(strict_types=1);

namespace App\Concretes;

use App\Entities\Task;
use App\Enums\TaskStatusEnum;
use App\Interfaces\TaskManagerInterface;

final readonly class TaskManager implements TaskManagerInterface
{
    public function __construct(private TaskStorageHandler $taskStorageHandler) {}

    /**
     * @inheritDoc
     */
    public function add(string $taskDescription): Task
    {
        return $this->taskStorageHandler->insert($taskDescription);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $taskId): ?Task
    {
        return $this->taskStorageHandler->delete($taskId);
    }

    /**
     * @inheritDoc
     * @return Task[]
     */
    public function list(?string $taskStatus = null): array
    {
        return $this->taskStorageHandler->list($taskStatus);
    }

    /**
     * @inheritDoc
     */
    public function markDone(int $taskId): ?Task
    {
        return $this->taskStorageHandler->update($taskId, ['status' => TaskStatusEnum::DONE]);
    }

    /**
     * @inheritDoc
     */
    public function markInProgress(int $taskId): ?Task
    {
        return $this->taskStorageHandler->update($taskId, ['status' => TaskStatusEnum::IN_PROGRESS]);
    }

    /**
     * @inheritDoc
     */
    public function update(int $taskId, string $newTaskDescription): ?Task
    {
        return $this->taskStorageHandler->update($taskId, ['description' => $newTaskDescription]);
    }
}
