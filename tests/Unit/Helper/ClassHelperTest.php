<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wexample\Helpers\Helper\ClassHelper;

class ClassHelperTest extends TestCase
{
    public function testClassNameIsBuiltFromAPath(): void
    {
        $this->assertSame(
            'This\\Is\\ATest\\ClassPath',
            ClassHelper::buildClassNameFromPath('this/is/a_test/class-path')
        );
    }

    public function testShortName(): void
    {
        $this->assertSame('User', ClassHelper::getShortName('App\\Entity\\User'));
        $this->assertSame('User', ClassHelper::getShortName('\\App\\Entity\\User'));
        $this->assertSame('User', ClassHelper::getShortName('App\\Entity\\User::getName'));
        $this->assertSame('Plain', ClassHelper::getShortName('Plain'));
        $this->assertSame('ClassHelperTest', ClassHelper::getShortName($this));
        $this->assertSame('class-helper-test', ClassHelper::getKebabName($this));
    }

    public function testAccessorNames(): void
    {
        $this->assertSame('getFirstName', ClassHelper::buildFieldGetterName('first_name'));
        $this->assertSame('getFirstName', ClassHelper::buildFieldGetterName('first-name'));
        // Asked twice: the remembered name is the one built the first time.
        $this->assertSame('getFirstName', ClassHelper::buildFieldGetterName('firstName'));
        $this->assertSame('getFirstName', ClassHelper::buildFieldGetterName('firstName'));
        $this->assertSame('setFirstName', ClassHelper::buildFieldSetterName('firstName'));
    }

    public function testGetterValueOrDefault(): void
    {
        $object = new class () {
            public function getName(): string
            {
                return 'Alice';
            }

            public function getBroken(): string
            {
                throw new RuntimeException('Unreadable.');
            }
        };

        $this->assertSame('Alice', ClassHelper::getFieldGetterValueOrDefault($object, 'name', 'x'));
        $this->assertSame('x', ClassHelper::getFieldGetterValueOrDefault($object, 'missing', 'x'));
        $this->assertSame('x', ClassHelper::getFieldGetterValueOrDefault($object, 'broken', 'x'));

        // A class string has the getter but no value to read through it.
        $this->assertSame('x', ClassHelper::getFieldGetterValueOrDefault($object::class, 'name', 'x'));
        $this->assertSame('x', ClassHelper::getFieldGetterValueOrDefault('No\\Such\\Class', 'name', 'x'));
    }
}
