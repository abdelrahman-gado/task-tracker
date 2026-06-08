<?php declare(strict_types=1);

namespace Tests\Unit\Concretes;

use App\Concretes\JsonFileStorageHandler;
use App\Interfaces\StorageInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonFileStorageHandler::class)]
final class JsonFileStorageHandlerTest extends TestCase
{
    private string $filePath;

    private StorageInterface $storage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filePath = __DIR__ . '/../../../tasks.test.json';
        $this->storage = new JsonFileStorageHandler($this->filePath);
        $this->refreshJsonFile();
    }

    protected function tearDown(): void
    {
        $this->refreshJsonFile();
    }

    private function refreshJsonFile(): void
    {
        chmod($this->filePath, 0o666);
        file_put_contents($this->filePath, json_encode([]));
    }

    #[Test]
    public function test_json_file_created_if_it_is_not_exist(): void
    {
        $this->assertFileExists($this->filePath);
    }

    #[Test]
    public function test_exception_thrown_if_cannot_create_json_file(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Error: can't create file");
        @new JsonFileStorageHandler('./invalid/invalid.json');
    }

    #[Test]
    public function test_store_add_json_data_to_file(): void
    {
        $items = [
            [
                'id' => 1,
                'description' => 'test',
                'status' => 'todo',
                'createdAt' => '2026-06-05T00:00:00+00:00',
                'updatedAt' => '2026-06-05T00:00:00+00:00',
            ],
        ];

        $this->storage->store($items);
        $fileData = file_get_contents($this->filePath);
        if ($fileData === false) {
            $this->fail('cannot get data from file');
        }

        $this->assertSame(
            json_decode($fileData, true),
            $items
        );
    }

    #[Test]
    public function test_store_on_json_file_having_already_data(): void
    {
        $items = [
            [
                'id' => 1,
                'description' => 'test',
                'status' => 'todo',
                'createdAt' => '2026-06-05T00:00:00+00:00',
                'updatedAt' => '2026-06-05T00:00:00+00:00',
            ],
        ];
        $this->storage->store($items);
        $items[] = [
            'id' => 2,
            'description' => 'test2',
            'status' => 'done',
            'createdAt' => '2026-06-05T00:00:00+00:00',
            'updatedAt' => '2026-06-06T00:00:00+00:00',
        ];
        $this->storage->store($items);
        $fileData = file_get_contents($this->filePath);
        if ($fileData === false) {
            $this->fail('cannot get data from file');
        }

        $this->assertSame(
            json_decode($fileData, true),
            $items
        );
    }

    #[Test]
    public function test_load_return_data_that_stored(): void
    {
        $items = [
            [
                'id' => 1,
                'description' => 'test',
                'status' => 'todo',
                'createdAt' => '2026-06-05T00:00:00+00:00',
                'updatedAt' => '2026-06-05T00:00:00+00:00',
            ],
        ];
        $this->storage->store($items);
        $this->assertSame(
            $this->storage->load(),
            $items
        );
    }

    #[Test]
    public function test_exception_thrown_when_load_cannot_read_content(): void
    {
        file_put_contents($this->filePath, '');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Error: can't load file");
        $this->storage->load();
    }
}
