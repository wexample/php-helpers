<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Class\Traits\Fixtures;

/**
 * Declares its suffix below the class that uses the trait.
 */
class UserProfileController extends AbstractNamedFixture
{
    protected static function getClassNameSuffix(): ?string
    {
        return 'Controller';
    }
}
