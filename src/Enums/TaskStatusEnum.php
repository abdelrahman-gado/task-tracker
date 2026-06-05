<?php declare(strict_types=1);

namespace App\Enums;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in-progress';
    case DONE = 'done';

    /**
     * @return array<string>
     */
    public static function getCaseValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValidStatus(?string $status): bool
    {
        return in_array($status, self::getCaseValues(), true);
    }
}
