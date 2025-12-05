<?php

namespace Wexample\Helpers\Helper;

class DirHelper
{
    /**
     * Creates a directory recursively.
     *
     * This function checks if the directory already exists. If not, it attempts
     * to create it recursively. If the directory cannot be created, a RuntimeException
     * is thrown.
     *
     * @param string $path The path of the directory to create.
     * @param int $mode The permissions mode (default is 0755).
     * @return bool Returns true if the directory exists or was successfully created.
     * @throws \RuntimeException If the directory creation fails.
     */
    public static function createDirRecursive(
        string $path,
        int $mode = 0755
    ): bool {
        if (is_dir($path)) {
            // The directory already exists.
            return true;
        }

        // Try to create the directory recursively.
        if (! mkdir($path, $mode, true)) {
            throw new \RuntimeException("Failed to create directory: " . $path);
        }

        return true;
    }
}
