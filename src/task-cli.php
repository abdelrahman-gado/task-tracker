<?php declare(strict_types=1);

namespace App;

use App\Concretes\TaskManager;
use App\Concretes\TaskTrackerCLIHandler;
use App\Interfaces\TaskManagerInterface;
use App\Interfaces\TaskTrackerIOHandlerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

function getTaskManager(): TaskManagerInterface
{
    return new TaskManager();
}

function getTaskTrackerApp(TaskManagerInterface $taskManager): TaskTrackerIOHandlerInterface
{
    return new TaskTrackerCLIHandler($taskManager);
}

$cliApp = getTaskTrackerApp(getTaskManager());
$cliApp->handleInput($argv[1] ?? null, array_slice($argv, 2));
