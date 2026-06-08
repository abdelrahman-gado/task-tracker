<?php declare(strict_types=1);

namespace Tests\Unit\Concretes;

use App\Concretes\TaskManager;
use App\Concretes\TaskStorageHandler;
use App\Entities\Task;
use App\Enums\TaskStatusEnum;
use DateTimeImmutable;
use DG\BypassFinals;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TaskManager::class)]
#[UsesClass(Task::class)]
#[UsesClass(TaskStatusEnum::class)]
final class TaskManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var MockInterface&TaskStorageHandler */
    private MockInterface $taskStorageMock;

    private TaskManager $taskManager;

    protected function setUp(): void
    {
        parent::setUp();
        BypassFinals::enable();
        $this->taskStorageMock = Mockery::mock(TaskStorageHandler::class);
        $this->taskManager = new TaskManager($this->taskStorageMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function test_add_returns_added_task(): void
    {
        $task = new Task(
            id: 1,
            description: $description = 'test',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('insert')
            ->with($description)
            ->once()
            ->andReturn($task);

        $taskExpected = $this->taskManager->add($description);
        $this->assertObjectEquals($taskExpected, $task);
    }

    #[Test]
    public function test_delete_returns_deleted_task(): void
    {
        $task = new Task(
            id: $taskId = 1,
            description: 'test',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('delete')
            ->with($taskId)
            ->once()
            ->andReturn($task);

        $taskExpected = $this->taskManager->delete($taskId);

        $this->assertInstanceOf(\App\Entities\Task::class, $taskExpected);
        $this->assertObjectEquals($taskExpected, $task);
    }

    #[Test]
    public function test_delete_returns_null_when_task_not_found(): void
    {
        new Task(
            id: $taskId = 100,
            description: 'test',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('delete')
            ->with($taskId)
            ->once()
            ->andReturn(null);

        $taskExpected = $this->taskManager->delete($taskId);
        $this->assertNotInstanceOf(\App\Entities\Task::class, $taskExpected);
    }

    #[Test]
    public function test_list_returns_all_tasks(): void
    {
        $task1 = new Task(
            id: 1,
            description: 'test 1',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );
        $task2 = new Task(
            id: 2,
            description: 'test 2',
            status: TaskStatusEnum::DONE,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('list')
            ->with(null)
            ->once()
            ->andReturn([$task1, $task2]);

        $tasksExpected = $this->taskManager->list();
        $this->assertEquals([$task1, $task2], $tasksExpected);
        $this->assertCount(2, $tasksExpected);
        $this->assertObjectEquals($tasksExpected[0], $task1);
    }

    #[Test]
    public function test_list_filters_by_task_status(): void
    {
        new Task(
            id: 1,
            description: 'test 1',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );
        $task = new Task(
            id: 2,
            description: 'test 2',
            status: TaskStatusEnum::DONE,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('list')
            ->with(TaskStatusEnum::DONE->value)
            ->once()
            ->andReturn([$task]);

        $tasksExpected = $this->taskManager->list(TaskStatusEnum::DONE->value);
        $this->assertEquals([$task], $tasksExpected);
        $this->assertCount(1, $tasksExpected);
        $this->assertObjectEquals($tasksExpected[0], $task);
    }

    #[Test]
    public function test_mark_done_updates_task_status(): void
    {
        $task = new Task(
            id: $taskId = 1,
            description: 'test',
            status: TaskStatusEnum::DONE,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('update')
            ->with($taskId, ['status' => TaskStatusEnum::DONE])
            ->once()
            ->andReturn($task);

        $taskExpected = $this->taskManager->markDone($taskId);
        $this->assertInstanceOf(\App\Entities\Task::class, $taskExpected);
        $this->assertObjectEquals($taskExpected, $task);
    }

    #[Test]
    public function test_mark_done_returns_null_when_task_not_found(): void
    {
        new Task(
            id: $taskId = 100,
            description: 'test',
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('update')
            ->with($taskId, ['status' => TaskStatusEnum::DONE])
            ->once()
            ->andReturn(null);

        $taskExpected = $this->taskManager->markDone($taskId);
        $this->assertNotInstanceOf(\App\Entities\Task::class, $taskExpected);
    }

    #[Test]
    public function test_mark_in_progress_updates_task_status(): void
    {
        $task = new Task(
            id: $taskId = 1,
            description: 'test',
            status: TaskStatusEnum::IN_PROGRESS,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('update')
            ->with($taskId, ['status' => TaskStatusEnum::IN_PROGRESS])
            ->once()
            ->andReturn($task);

        $taskExpected = $this->taskManager->markInProgress($taskId);
        $this->assertInstanceOf(\App\Entities\Task::class, $taskExpected);
        $this->assertObjectEquals($taskExpected, $task);
    }

    #[Test]
    public function test_update_updates_task_description(): void
    {
        $task = new Task(
            id: $taskId = 1,
            description: $newDescription = 'new test',
            status: TaskStatusEnum::IN_PROGRESS,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('update')
            ->with($taskId, ['description' => 'new test'])
            ->once()
            ->andReturn($task);

        $taskExpected = $this->taskManager->update($taskId, $newDescription);
        $this->assertInstanceOf(\App\Entities\Task::class, $taskExpected);
        $this->assertObjectEquals($taskExpected, $task);
    }

    #[Test]
    public function test_update_returns_null_when_task_not_found(): void
    {
        new Task(
            id: $taskId = 100,
            description: $newDescription = 'new test',
            status: TaskStatusEnum::IN_PROGRESS,
            createdAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05T00:00:00+00:00')
        );

        $this->taskStorageMock->shouldReceive('update')
            ->with($taskId, ['description' => 'new test'])
            ->once()
            ->andReturn(null);

        $taskExpected = $this->taskManager->update($taskId, $newDescription);
        $this->assertNotInstanceOf(\App\Entities\Task::class, $taskExpected);
    }
}
