<?php declare(strict_types=1);

namespace Tests\Unit\Entities;

use App\Entities\Task;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Task::class)]
final class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new Task(
            id: 1,
            description: 'test',
            createdAt: new DateTimeImmutable('2026-06-05 00:00:00'),
            updatedAt: new DateTimeImmutable('2026-06-05 00:00:00'),
        );
    }

    #[Test]
    public function test_toArray_convert_task_to_array(): void
    {
        $this->assertSame(
            [
                'id' => 1,
                'description' => 'test',
                'status' => 'todo',
                'createdAt' => '2026-06-05T00:00:00+00:00',
                'updatedAt' =>  '2026-06-05T00:00:00+00:00',
            ],
            $this->task->toArray()
        );
    }

    #[Test]
    public function test_fromArray_convert_array_to_task(): void
    {
        $taskArray = $this->task->toArray();
        $this->assertObjectEquals($this->task, Task::fromArray($taskArray));
    }

    #[Test]
    public function test_toString_convert_task_correctly_to_string(): void
    {
        $this->assertSame(
            'Task #1: test [todo] (created at: 2026-06-05T00:00:00+00:00, updated at: 2026-06-05T00:00:00+00:00)',
            $this->task->__toString()
        );
    }
}
