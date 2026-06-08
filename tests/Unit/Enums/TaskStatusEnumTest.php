<?php declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\TaskStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TaskStatusEnum::class)]
final class TaskStatusEnumTest extends TestCase
{
    #[Test]
    public function test_getCaseValues_return_array_of_current_values(): void
    {
        $this->assertSame(
            ['todo', 'in-progress', 'done'],
            TaskStatusEnum::getCaseValues()
        );
    }

    #[Test]
    public function test_isValidStatus_return_true(): void
    {
        $this->assertTrue(TaskStatusEnum::isValidStatus('todo'));
    }
}
