<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Helper;

use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wexample\Helpers\Helper\JsonHelper;

class JsonHelperTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = tempnam(sys_get_temp_dir(), 'json-helper-');
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
    }

    /**
     * @return list<array{int}>
     */
    public static function chunkSizes(): array
    {
        return [[1], [2], [7], [1048576]];
    }

    #[DataProvider('chunkSizes')]
    public function testTheItemsAreReadWhateverTheChunks(int $chunkSize): void
    {
        $items = [
            ['id' => '1', 'title' => 'Commas, [brackets] and {braces} in a string', 'list' => [1, [2, 3]]],
            ['id' => '2', 'title' => 'An escaped quote \" and a backslash \\', 'nested' => ['a' => ['b' => null]]],
            'a string',
            42,
            true,
            ['id' => '3', 'title' => 'Accents: é, ’, «»'],
        ];
        file_put_contents($this->path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->assertSame($items, iterator_to_array(JsonHelper::readArrayItems($this->path, $chunkSize), false));
    }

    public function testAnEmptyArrayHoldsNoItem(): void
    {
        file_put_contents($this->path, " [ \n ] ");

        $this->assertSame([], iterator_to_array(JsonHelper::readArrayItems($this->path, 1), false));
    }

    public function testAnObjectIsNoArray(): void
    {
        file_put_contents($this->path, '{"a": 1}');

        $this->expectException(RuntimeException::class);
        iterator_to_array(JsonHelper::readArrayItems($this->path));
    }

    public function testAFileCutShortIsAnError(): void
    {
        file_put_contents($this->path, '[{"a": 1}, {"b": ');

        $items = JsonHelper::readArrayItems($this->path, 4);
        $this->assertSame(['a' => 1], $items->current());

        $this->expectException(RuntimeException::class);
        $items->next();
    }

    public function testAnInvalidItemIsAnError(): void
    {
        file_put_contents($this->path, '[{"a": 1}, {b: 2}]');

        $this->expectException(JsonException::class);
        iterator_to_array(JsonHelper::readArrayItems($this->path));
    }

    public function testAMissingFileIsAnError(): void
    {
        $this->expectException(RuntimeException::class);
        iterator_to_array(JsonHelper::readArrayItems($this->path.'-missing'));
    }
}
