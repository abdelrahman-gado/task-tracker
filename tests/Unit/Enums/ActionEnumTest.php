<?php declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ActionEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActionEnum::class)]
final class ActionEnumTest extends TestCase
{
    #[Test]
    public function test_is_equal_return_true_in_case_matching(): void
    {
        $this->assertTrue(ActionEnum::isEqual('update', ActionEnum::UPDATE));
    }

    #[Test]
    public function test_is_equal_return_false_in_case_of_invalid_action_string(): void
    {
        $this->assertFalse(ActionEnum::isEqual('insert', ActionEnum::UPDATE));
    }
}
