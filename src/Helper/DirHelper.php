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

    /**
     * Removes a directory recursively.
     *
     * This function deletes a directory and all its contents (files and subdirectories).
     * If the directory does not exist, the function returns without error.
     *
     * @param string $dir The path of the directory to remove.
     * @return void
     */
    public static function removeDirRecursive(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? self::removeDirRecursive($path) : unlink($path);
        }
        rmdir($dir);
    }
}
