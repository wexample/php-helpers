<?php

namespace Wexample\Helpers\Class\Traits;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;

trait HasShortClassNameClassTrait
{
    protected static function getClassNameSuffix(): ?string
    {
        return null;
    }

    public static function getShortClassName(): string
    {
        $shortName = ClassHelper::getShortName(static::class);

        if ($suffix = self::getClassNameSuffix()) {
            return TextHelper::removeSuffix($shortName, $suffix);
        }

        return $shortName;
    }
}
