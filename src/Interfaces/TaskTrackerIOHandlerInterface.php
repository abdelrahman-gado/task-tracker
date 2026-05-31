<?php declare(strict_types=1);

namespace App\Interfaces;

interface TaskTrackerIOHandlerInterface
{
    /**
     * @param string[]|null $data
     */
    public function handleInput(?string $action, ?array $data): void;
}
