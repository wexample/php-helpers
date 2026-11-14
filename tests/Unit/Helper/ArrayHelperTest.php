<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Wexample\Helpers\Helper\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    public function testAListIsNotAssociative(): void
    {
        $this->assertFalse(ArrayHelper::isAssociative(['a', 'b']));
        $this->assertFalse(ArrayHelper::isAssociative([]));
    }

    public function testNamedOrHoledKeysAreAssociative(): void
    {
        $this->assertTrue(ArrayHelper::isAssociative(['a' => 1]));
        $this->assertTrue(ArrayHelper::isAssociative([1 => 'a', 2 => 'b']));
        $this->assertTrue(ArrayHelper::isAssociative([1 => 'b', 0 => 'a']));
    }

    public function testCanonicalizeSortsKeysAndListValues(): void
    {
        $this->assertSame(
            ['a' => ['x', 'y'], 'b' => []],
            ArrayHelper::canonicalize(['b' => [], 'a' => ['y', 'x']])
        );
    }

    public function testItemIsFoundByAStringPath(): void
    {
        $data = ['a' => ['b' => ['c' => 1]]];

        $this->assertSame(1, ArrayHelper::getItemByPath($data, 'a.b.c'));
        $this->assertSame(['c' => 1], ArrayHelper::getItemByPath($data, 'a/b', separator: '/'));
        $this->assertSame('none', ArrayHelper::getItemByPath($data, 'a.x', 'none'));
    }

    public function testItemIsFoundByAListOfKeys(): void
    {
        $data = ['a.b' => ['c' => 1], 'list' => ['first', 'second']];

        // A key holding the separator is reachable once the path is given as keys.
        $this->assertSame(1, ArrayHelper::getItemByPath($data, ['a.b', 'c']));
        $this->assertSame('second', ArrayHelper::getItemByPath($data, ['list', 1]));
        $this->assertSame('none', ArrayHelper::getItemByPath($data, ['a.b', 'x'], 'none'));
    }

    public function testFlattenJoinsNestedKeys(): void
    {
        $this->assertSame(
            ['a.b' => 1, 'a.c.d' => 2, 'e' => 3],
            ArrayHelper::flattenArray(['a' => ['b' => 1, 'c' => ['d' => 2]], 'e' => 3])
        );
        $this->assertSame(
            ['p.a' => 1],
            ArrayHelper::flattenArray(['a' => 1], 'p')
        );
    }

    public function testFlattenKeepsAZeroPrefix(): void
    {
        $this->assertSame(
            ['0.name' => 'a', '1.name' => 'b'],
            ArrayHelper::flattenArray([['name' => 'a'], ['name' => 'b']])
        );
        $this->assertSame(
            ['0.a' => 1],
            ArrayHelper::flattenArray(['a' => 1], '0')
        );
    }

    public function testFlattenKeepsIntegerKeys(): void
    {
        $this->assertSame(
            [5 => 'a', 'b.0' => 'x', 9 => 'c'],
            ArrayHelper::flattenArray([5 => 'a', 'b' => ['x'], 9 => 'c'])
        );
    }

    public function testRemoveItemReindexes(): void
    {
        $this->assertSame(['a', 'c'], ArrayHelper::removeItem('b', ['a', 'b', 'c']));
    }

    public function testAreSameMayIgnoreMissingEmptyArrays(): void
    {
        $this->assertFalse(ArrayHelper::areSame(['a' => 1, 'b' => []], ['a' => 1]));
        $this->assertTrue(ArrayHelper::areSame(['a' => 1, 'b' => []], ['a' => 1], true));
    }
}
