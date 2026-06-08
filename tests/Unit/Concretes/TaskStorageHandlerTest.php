<?php declare(strict_types=1);

namespace Tests\Unit\Concretes;

use App\Concretes\TaskStorageHandler;
use App\Entities\Task;
use App\Enums\TaskStatusEnum;
use App\Interfaces\StorageInterface;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TaskStorageHandler::class)]
#[UsesClass(Task::class)]
#[UsesClass(TaskStatusEnum::class)]
final class TaskStorageHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface&StorageInterface $storageAbstractMock;

    private TaskStorageHandler $taskStorageHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storageAbstractMock = Mockery::mock(StorageInterface::class);
        $this->taskStorageHandler = new TaskStorageHandler($this->storageAbstractMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    #[Test]
    public function test_load_method_return_an_array_of_tasks(): void
    {
        $this->storageAbstractMock->expects()
            ->load()
            ->once()
            ->andReturn(
                [
                    '1' => [
                        'id' => 1,
                        'description' => 'test',
                        'status' => 'todo',
                        'createdAt' => '2026-06-05T00:00:00+00:00',
                        'updatedAt' => '2026-06-05T00:00:00+00:00',
                    ],
                ]
            );

        $tasks = $this->taskStorageHandler->load();
        $this->assertEquals(
            $tasks,
            [1 => new Task(1, 'test', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'))]
        );
        $this->assertCount(1, $tasks);
    }

    #[Test]
    public function test_store_method_calls_store_method_of_underlying_storage_object(): void
    {
        $task = new Task(1, 'test', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([1 => $task->toArray()])
            ->once();

        $this->taskStorageHandler->store([1 => $task]);
    }

    #[Test]
    public function test_task_storage_can_insert_a_task(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray()]);

        $this->storageAbstractMock
            ->expects()
            ->getLastId()
            ->once()
            ->andReturn(1);

        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with(
                Mockery::on(function (array $items) use ($task1): bool {
                    /**
                     * @var array<int, array{id: int, description: string, status: string, createdAt: string, updatedAt: string}> $items
                     */
                    return $items[1] === $task1->toArray()
                        && $items[2]['id'] === 2
                        && $items[2]['description'] === 'test 2'
                        && $items[2]['status'] === 'todo';
                })
            )
            ->once();

        $taskExpected = $this->taskStorageHandler->insert('test 2');
        $this->assertObjectEquals(
            $taskExpected,
            $task2
        );
    }

    #[Test]
    public function test_task_storage_can_delete_a_task(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);


        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([2 => $task2->toArray()])
            ->once();

        $taskExpected = $this->taskStorageHandler->delete(1);
        $this->assertNotNull($taskExpected);
        $this->assertObjectEquals(
            $taskExpected,
            $task1
        );
    }

    #[Test]
    public function test_delete_returns_null_when_task_id_not_found(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);


        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([1 => $task1->toArray(), 2 => $task2->toArray()])
            ->never();


        $this->assertNull($this->taskStorageHandler->delete(222));
    }

    #[Test]
    public function test_list_without_status_filter_returns_tasks(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);

        $tasks = $this->taskStorageHandler->list();
        $this->assertCount(2, $tasks);
        $this->assertEquals([1 => $task1, 2 => $task2], $tasks);
        $this->assertObjectEquals($task1, $tasks[1]);
        $this->assertObjectEquals($task2, $tasks[2]);
    }

    #[Test]
    public function test_list_with_status_filter_returns_filtered_status_tasks(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);

        $tasks = $this->taskStorageHandler->list(TaskStatusEnum::TODO->value);
        $this->assertCount(1, $tasks);
        $this->assertEquals([2 => $task2], $tasks);
        $this->assertObjectEquals($task2, $tasks[2]);
        $this->assertArrayNotHasKey(1, $tasks);
    }

    #[Test]
    public function test_update_returns_null_when_task_needed_to_be_updated_is_not_found(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);


        $this->assertNull($this->taskStorageHandler->update(222, []));
    }

    #[Test]
    public function test_update_updates_task_status(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);

        $task1->status = TaskStatusEnum::IN_PROGRESS;
        $task1->updatedAt = new DateTimeImmutable();

        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([1 => $task1->toArray(), 2 => $task2->toArray()])
            ->once();

        $taskExpected = $this->taskStorageHandler->update(1, ['status' => TaskStatusEnum::IN_PROGRESS]);
        $this->assertNotNull($taskExpected);
        $this->assertObjectEquals(
            $taskExpected,
            $task1
        );
        $this->assertSame($taskExpected->status, $task1->status);
        $this->assertSame($taskExpected->id, $task1->id);
    }

    #[Test]
    public function test_update_updates_task_description(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);

        $newDescription = 'newDescription';
        $task2->description = $newDescription;
        $task2->updatedAt = new DateTimeImmutable();

        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([1 => $task1->toArray(), 2 => $task2->toArray()])
            ->once();

        $taskExpected = $this->taskStorageHandler->update(2, ['description' => $newDescription]);
        $this->assertNotNull($taskExpected);
        $this->assertObjectEquals(
            $taskExpected,
            $task2
        );
        $this->assertSame($taskExpected->description, $task2->description);
        $this->assertSame($taskExpected->id, $task2->id);
        $this->assertSame($taskExpected->status, $task2->status);
    }

    #[Test]
    public function test_update_skips_not_found_properties(): void
    {
        $task1 = new Task(1, 'test 1', TaskStatusEnum::DONE, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));
        $task2 = new Task(2, 'test 2', TaskStatusEnum::TODO, new DateTimeImmutable('2026-06-05T00:00:00+00:00'), new DateTimeImmutable('2026-06-05T00:00:00+00:00'));

        $this->storageAbstractMock
            ->expects()
            ->load()
            ->once()
            ->andReturn([1 => $task1->toArray(), 2 => $task2->toArray()]);

        $newDescription = 'newDescription';
        $status = TaskStatusEnum::TODO;
        $task1->description = $newDescription;
        $task1->status = $status;
        $task1->updatedAt = new DateTimeImmutable();

        $this->storageAbstractMock
            ->expects()
            ->store()
            ->with([1 => $task1->toArray(), 2 => $task2->toArray()])
            ->once();

        $taskExpected = $this->taskStorageHandler->update(
            1,
            ['description' => $newDescription, 'status' => TaskStatusEnum::TODO, 'not_found_property' => 'wrong']
        );
        $this->assertNotNull($taskExpected);
        $this->assertObjectEquals(
            $taskExpected,
            $task1
        );
        $this->assertSame($taskExpected->description, $task1->description);
        $this->assertSame($taskExpected->id, $task1->id);
        $this->assertSame($taskExpected->status, $task1->status);
    }
}
