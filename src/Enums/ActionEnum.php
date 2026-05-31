<?php declare(strict_types=1);

namespace App\Enums;

enum ActionEnum: string
{
    case ADD = 'add';
    case LIST = 'list';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case MARK_DONE = 'mark-done';
    case MARK_IN_PROGRESS = 'mark-in-progress';
    case HELP = 'help';

    public static function isEqual(string $action, ActionEnum $actionEnum): bool
    {
        return strtolower($action) === $actionEnum->value;
    }
}
