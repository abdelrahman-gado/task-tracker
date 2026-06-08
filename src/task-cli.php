<?php declare(strict_types=1);

namespace App;

use App\Concretes\JsonFileStorageHandler;
use App\Concretes\TaskManager;
use App\Concretes\TaskStorageHandler;
use App\Concretes\TaskTrackerCLIHandler;
use App\Interfaces\TaskManagerInterface;
use App\Interfaces\TaskTrackerIOHandlerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

function getStorageHandler(): TaskStorageHandler
{
    $handler = new JsonFileStorageHandler(__DIR__ . '/../tasks.json');
    return new TaskStorageHandler($handler);
}

function getTaskManager(TaskStorageHandler $taskStorageHandler): TaskManagerInterface
{
    return new TaskManager($taskStorageHandler);
}

function getTaskTrackerApp(TaskManagerInterface $taskManager): TaskTrackerIOHandlerInterface
{
    return new TaskTrackerCLIHandler($taskManager);
}

$storageHandler = getStorageHandler();
$cliApp = getTaskTrackerApp(getTaskManager($storageHandler));
$cliApp->handleInput($argv[1] ?? null, array_slice($argv, 2));
