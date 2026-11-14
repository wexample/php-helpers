<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Wexample\Helpers\Helper\DirHelper;

class DirHelperTest extends TestCase
{
    public function testDirectoryIsCreatedOnceAndThenFound(): void
    {
        $root = sys_get_temp_dir() . '/php-helpers-' . bin2hex(random_bytes(4));
        $path = $root . '/a/b';

        try {
            $this->assertTrue(DirHelper::createDirRecursive($path));
            $this->assertDirectoryExists($path);
            $this->assertTrue(DirHelper::createDirRecursive($path));
        } finally {
            DirHelper::removeDirRecursive($root);
        }

        $this->assertDirectoryDoesNotExist($root);
    }
}
