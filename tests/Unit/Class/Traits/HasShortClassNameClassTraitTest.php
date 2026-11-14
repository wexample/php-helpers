<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Class\Traits;

use PHPUnit\Framework\TestCase;
use Wexample\Helpers\Tests\Unit\Class\Traits\Fixtures\UserProfileController;

class HasShortClassNameClassTraitTest extends TestCase
{
    public function testASuffixDeclaredByASubclassIsRemoved(): void
    {
        $this->assertSame('UserProfile', UserProfileController::getShortClassName());
        $this->assertSame('user_profile', UserProfileController::getSnakeShortClassName());
    }
}
