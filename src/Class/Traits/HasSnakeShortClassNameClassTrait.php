<?php

namespace Wexample\Helpers\Class\Traits;

use Wexample\Helpers\Helper\TextHelper;

trait HasSnakeShortClassNameClassTrait
{
    use HasShortClassNameClassTrait;

    public static function getSnakeShortClassName(): string
    {
        return TextHelper::toSnake(static::getShortClassName());
    }
}
