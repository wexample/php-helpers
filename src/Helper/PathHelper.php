<?php

namespace Wexample\Helpers\Helper;

use function realpath;
use function strlen;
use function substr;

class PathHelper
{
    public static function relativeTo(
        string $path,
        string $basePath,
        string $pathSeparator = FileHelper::FOLDER_SEPARATOR
    ): string
    {
        return substr(
            $path,
            strlen(
                realpath(
                    $basePath
                ) . $pathSeparator
            )
        );
    }

    public static function join(array $pathParts): string
    {
        return implode(FileHelper::FOLDER_SEPARATOR, $pathParts);
    }

    public static function getPathParts(
        string $path,
        string $separator,
        $offset = 2
    ): array
    {
        return array_slice(
            explode(
                $separator,
                $path
            ),
            $offset
        );
    }

    public static function getCousin(
        string $fullPath,
        string $sourceBasePath,
        string $cousinBasePath,
        ?string $classSuffix = null,
        ?string $cousinSuffix = null,
        ?string $separator = FileHelper::FOLDER_SEPARATOR
    ): string
    {
        $parts = static::getPathParts(
            $fullPath,
            separator: $separator,
            offset: count(explode($separator, $sourceBasePath)) - 1
        );

        $classBase = implode($separator, $parts);

        // Remove suffix if exists.
        $classBase = $classSuffix ? substr(
            $classBase,
            0,
            -strlen($classSuffix)
        ) : $classBase;

        return $cousinBasePath . $classBase . $cousinSuffix;
    }
}
