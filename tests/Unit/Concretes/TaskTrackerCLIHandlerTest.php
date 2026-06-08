<?php declare(strict_types=1);

namespace Tests\Unit\Concretes;

use App\Concretes\TaskTrackerCLIHandler;
use App\Entities\Task;
use App\Enums\ActionEnum;
use App\Enums\TaskStatusEnum;
use App\Interfaces\TaskManagerInterface;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TaskTrackerCLIHandler::class)]
#[UsesClass(ActionEnum::class)]
#[UsesClass(Task::class)]
#[UsesClass(TaskStatusEnum::class)]
final class TaskTrackerCLIHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface&TaskManagerInterface $taskManagerMock;

    private TaskTrackerCLIHandler $taskTrackerCLIHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskManagerMock = Mockery::mock(TaskManagerInterface::class);
        $this->taskTrackerCLIHandler = new TaskTrackerCLIHandler($this->taskManagerMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function test_add_action_outputs_success_msg_after_adding_task(): void
    {
        $task = new Task(
            id: 1,
            description: $taskDescription = 'test description',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskManagerMock->expects()
            ->add()
            ->with($taskDescription)
            ->once()
            ->andReturn($task);

        $this->expectOutputString("Task added successfully (ID: 1)\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::ADD->value, [$taskDescription]);
    }

    #[Test]
    public function test_add_action_outputs_error_msg_when_not_pass_description(): void
    {
        $this->expectOutputString(<<<ERROR
            Error: Task description is required for 'add' action.
            use 'php task-cli.php help' for usage instructions.\n
            ERROR);
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::ADD->value, []);
    }

    #[Test]
    public function test_list_action_outputs_tasks_list_without_status_filter(): void
    {
        $task1 = new Task(1, 'task1');
        $task2 = new Task(2, 'task2');

        $this->taskManagerMock
            ->expects()
            ->list()
            ->with(null)
            ->andReturn([$task1, $task2]);

        $this->expectOutputString(
            $task1 . "\n" . $task2 . "\n"
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::LIST->value, []);
    }

    #[Test]
    public function test_list_action_outputs_tasks_list_with_done_status_filter(): void
    {
        $task = new Task(1, 'task1', TaskStatusEnum::DONE);
        new Task(2, 'task2');

        $this->taskManagerMock
            ->expects()
            ->list()
            ->with('done')
            ->andReturn([$task]);

        $this->expectOutputString(
            $task . "\n"
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::LIST->value, ['done']);
    }

    #[Test]
    public function test_list_action_outputs_err_msg_when_status_filter_is_invalid_status(): void
    {
        $invalidTaskStatus = 'invalid_task_status';
        $this->expectOutputString(
            sprintf("Error: Invalid task status '%s'. Valid statuses are: ", $invalidTaskStatus)
                . implode(', ', TaskStatusEnum::getCaseValues()) . ".\n"
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::LIST->value, [$invalidTaskStatus]);
    }

    #[Test]
    public function test_update_action_outputs_task_updated(): void
    {
        $task = new Task(
            id: 1,
            description: 'new description',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskManagerMock->expects()
            ->update()
            ->with($task->id, $task->description)
            ->once()
            ->andReturn($task);

        $this->expectOutputString("Task with ID {$task->id} has been updated successfully.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::UPDATE->value, ['1', 'new description']);
    }


    #[Test]
    public function test_update_action_outputs_err_msg_if_task_not_found(): void
    {
        $nonExistTaskId = 100;
        $this->taskManagerMock->expects()
            ->update()
            ->with($nonExistTaskId, 'new description')
            ->once()
            ->andReturn(null);

        $this->expectOutputString("Error: Task with ID {$nonExistTaskId} not found.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::UPDATE->value, [(string) $nonExistTaskId, 'new description']);
    }

    #[Test]
    public function test_update_action_outputs_err_msg_when_missing_args(): void
    {
        $this->expectOutputString(
            <<<ERROR
                Error: Task description is required for 'update' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::UPDATE->value, []);
    }

    #[Test]
    public function test_delete_action_outputs_success_msg_when_task_deleted(): void
    {
        $task = new Task(
            id: 1,
            description: 'new description',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskManagerMock
            ->expects()
            ->delete()
            ->with($task->id)
            ->andReturn($task);

        $this->expectOutputString("Task with ID {$task->id} has been deleted successfully.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::DELETE->value, [(string) $task->id]);
    }

    #[Test]
    public function test_delete_action_outputs_err_msg_when_task_not_found(): void
    {
        $nonExistTaskId = 100;
        $this->taskManagerMock
            ->expects()
            ->delete()
            ->with($nonExistTaskId)
            ->andReturn(null);

        $this->expectOutputString("Error: Task with ID {$nonExistTaskId} not found.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::DELETE->value, [(string) $nonExistTaskId]);
    }

    #[Test]
    public function test_delete_action_outputs_err_msg_when_missing_args(): void
    {
        $this->expectOutputString(
            <<<ERROR
                Error: Task description is required for 'delete' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::DELETE->value, []);
    }

    #[Test]
    public function test_mark_done_action_outputs_success_msg_when_task_marked_done(): void
    {
        $task = new Task(
            id: 1,
            description: 'description',
            status: TaskStatusEnum::DONE,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskManagerMock
            ->expects()
            ->markDone()
            ->with($task->id)
            ->andReturn($task);

        $this->expectOutputString("Task with ID {$task->id} has been marked as done successfully.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_DONE->value, [(string) $task->id]);
    }

    #[Test]
    public function test_mark_done_action_outputs_err_msg_when_task_not_found(): void
    {
        $nonExistTaskId = 100;
        $this->taskManagerMock
            ->expects()
            ->markDone()
            ->with($nonExistTaskId)
            ->andReturn(null);

        $this->expectOutputString("Error: Task with ID {$nonExistTaskId} not found.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_DONE->value, [(string) $nonExistTaskId]);
    }

    #[Test]
    public function test_mark_done_action_outputs_err_msg_when_missing_args(): void
    {
        $this->expectOutputString(
            <<<ERROR
                Error: Task description is required for 'mark-done' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_DONE->value, []);
    }

    #[Test]
    public function test_mark_in_progress_action_outputs_success_msg_when_task_marked(): void
    {
        $task = new Task(
            id: 1,
            description: 'description',
            status: TaskStatusEnum::IN_PROGRESS,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskManagerMock
            ->expects()
            ->markInProgress()
            ->with($task->id)
            ->andReturn($task);

        $this->expectOutputString("Task with ID {$task->id} has been marked as in progress successfully.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_IN_PROGRESS->value, [(string) $task->id]);
    }

    #[Test]
    public function test_mark_in_progress_action_outputs_err_msg_when_task_not_found(): void
    {
        $nonExistTaskId = 100;
        $this->taskManagerMock
            ->expects()
            ->markInProgress()
            ->with($nonExistTaskId)
            ->andReturn(null);

        $this->expectOutputString("Error: Task with ID {$nonExistTaskId} not found.\n");
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_IN_PROGRESS->value, [(string) $nonExistTaskId]);
    }

    #[Test]
    public function test_mark_in_progress_action_outputs_err_msg_when_missing_args(): void
    {
        $this->expectOutputString(
            <<<ERROR
                Error: Task description is required for 'mark-in-progress' action.
                use 'php task-cli.php help' for usage instructions.\n
                ERROR
        );
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::MARK_IN_PROGRESS->value, []);
    }

    #[Test]
    public function test_help_action_outputs_info(): void
    {
        $helpInfo = <<<HELP
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

        $this->expectOutputString($helpInfo);
        $this->taskTrackerCLIHandler->handleInput(ActionEnum::HELP->value, []);
    }
}
