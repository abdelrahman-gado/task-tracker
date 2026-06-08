<?php declare(strict_types=1);

namespace App\Concretes;

use App\Enums\ActionEnum;
use App\Enums\TaskStatusEnum;
use App\Interfaces\TaskManagerInterface;
use App\Interfaces\TaskTrackerIOHandlerInterface;

final readonly class TaskTrackerCLIHandler implements TaskTrackerIOHandlerInterface
{
    public function __construct(private TaskManagerInterface $taskManager) {}

    /**
     * @inheritDoc
     * @param string[]|null $args
     */
    public function handleInput(?string $action, ?array $args = []): void
    {
        $action = $action ? strtolower($action) : '';
        match ($action) {
            ActionEnum::ADD->value => $this->addAction($args),
            ActionEnum::LIST ->value => $this->listAction($args),
            ActionEnum::UPDATE->value => $this->updateAction($args),
            ActionEnum::DELETE->value => $this->deleteAction($args),
            ActionEnum::MARK_DONE->value => $this->markDoneAction($args),
            ActionEnum::MARK_IN_PROGRESS->value => $this->markInProgressAction($args),
            default => $this->helpAction(),
        };
    }

    /**
     * @param string[]|null $args
     */
    private function addAction(?array $args): void
    {
        $taskDescription = $args[0] ?? null;
        if (!$taskDescription) {
            echo <<<ERROR
                Error: Task description is required for 'add' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR;
            return;
        }

        $task = $this->taskManager->add($taskDescription);
        echo "Task added successfully (ID: {$task->id})\n";
    }

    /**
     * @param string[]|null $args
     */
    private function listAction(?array $args): void
    {
        $taskStatus = $args[0] ?? null;
        if ($taskStatus && !TaskStatusEnum::isValidStatus($taskStatus)) {
            echo sprintf("Error: Invalid task status '%s'. Valid statuses are: ", $taskStatus)
                . implode(', ', TaskStatusEnum::getCaseValues()) . ".\n";
            return;
        }

        $tasks = $this->taskManager->list($taskStatus);
        foreach ($tasks as $task) {
            echo $task . "\n";
        }
    }

    /**
     * @param string[]|null $args
     */
    private function updateAction(?array $args): void
    {
        $taskId = $args[0] ?? null;
        $newTaskDescription = $args[1] ?? null;
        if (!$taskId || !$newTaskDescription) {
            echo <<<ERROR
                Error: Task description is required for 'update' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR;
            return;
        }

        $task = $this->taskManager->update((int) $taskId, $newTaskDescription);
        if ($task instanceof \App\Entities\Task) {
            echo "Task with ID {$task->id} has been updated successfully.\n";
        } else {
            echo "Error: Task with ID {$taskId} not found.\n";
        }
    }

    /**
     * @param string[]|null $args
     */
    private function deleteAction(?array $args): void
    {
        $taskId = $args[0] ?? null;
        if (!$taskId) {
            echo <<<ERROR
                Error: Task description is required for 'delete' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR;
            return;
        }

        $task = $this->taskManager->delete((int) $taskId);
        if ($task instanceof \App\Entities\Task) {
            echo "Task with ID {$task->id} has been deleted successfully.\n";
        } else {
            echo "Error: Task with ID {$taskId} not found.\n";
        }
    }

    /**
     * @param string[]|null $args
     */
    private function markDoneAction(?array $args): void
    {
        $taskId = $args[0] ?? null;
        if (!$taskId) {
            echo <<<ERROR
                Error: Task description is required for 'mark-done' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR;
            return;
        }

        $task = $this->taskManager->markDone((int) $taskId);
        if ($task instanceof \App\Entities\Task) {
            echo "Task with ID {$task->id} has been marked as done successfully.\n";
        } else {
            echo "Error: Task with ID {$taskId} not found.\n";
        }
    }

    /**
     * @param string[]|null $args
     */
    private function markInProgressAction(?array $args): void
    {
        $taskId = $args[0] ?? null;
        if (!$taskId) {
            echo <<<ERROR
                Error: Task description is required for 'mark-in-progress' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR;
            return;
        }

        $task = $this->taskManager->markInProgress((int) $taskId);
        if ($task instanceof \App\Entities\Task) {
            echo "Task with ID {$task->id} has been marked as in progress successfully.\n";
        } else {
            echo "Error: Task with ID {$taskId} not found.\n";
        }
    }


    private function helpAction(): void
    {
        echo <<<HELP
                task-cli - A simple task tracker CLI application.

                Usage:
                    php task-cli.php <action> [options]

                Actions:
                    add <task_description> - Add a new task.
                    list - List all tasks.
                    list <task_status> - List all tasks by status ('done', 'todo', 'in-progress').
                    help - Display this help message.
                    update <task_id> <new_task_description> - Update an existing task.
                    delete <task_id> - Delete a task.
                    mark-done <task_id> - Mark a task as done.
                    mark-in-progress <task_id> - Mark a task as in progress.

                Options:
                    <task_description> - A string describing the task to be added or updated.
                    <task_id> - The ID of the task to be updated, deleted, or marked
                    <task_status> - The status of the tasks to be listed ('done', 'todo', 'in-progress')
                    <new_task_description> - A string describing the new task description for update action.

                Examples:
                    php task-cli.php add "Buy groceries"
                    php task-cli.php update 1 "Buy groceries and cook dinner"
                    php task-cli.php delete 1
                    php task-cli.php mark-in-progress 1
                    php task-cli.php mark-done 1
                    php task-cli.php list
                    php task-cli.php list done
                    php task-cli.php list todo
                    php task-cli.php list in-progress\n
            HELP;
    }
}
