<?php

namespace Wexample\Helpers\Helper;

class FileHelper
{
    final public const FOLDER_SEPARATOR = '/';
    final public const EXTENSION_SEPARATOR = '.';

    final public const FILE_EXTENSION_CSV = 'csv';
    final public const FILE_EXTENSION_PDF = 'pdf';
    final public const FILE_EXTENSION_PHP = 'php';
    final public const FILE_EXTENSION_YML = 'yml';
    final public const FILE_EXTENSION_VUE = 'vue';
    final public const FILE_EXTENSION_SVG = 'svg';
    final public const FILE_EXTENSION_TXT = 'txt';
    final public const FILE_EXTENSION_XLSX = 'xlsx';

    /**
     * Writes data to a file, creating the target directory recursively if it doesn't exist.
     *
     * This function is a wrapper around PHP's file_put_contents() function. Before writing,
     * it ensures that the directory where the file is to be written exists. If not, it creates
     * the directory recursively.
     *
     * @param string $path The full path to the file.
     * @param string $data The data to write.
     * @param int $flags Optional flags (e.g. FILE_APPEND).
     * @param resource|null $context Optional context resource.
     *
     * @return int|false The number of bytes written, or false on failure.
     *
     * @throws \RuntimeException If the directory creation fails.
     */
    public static function putContentsRecursive(
        string $path,
        string $data,
        int $flags = 0,
        $context = null
    ): false|int
    {
        $dir = dirname($path);

        DirHelper::createDirRecursive($dir);

        if ($context === null) {
            return file_put_contents($path, $data, $flags);
        }

        return file_put_contents($path, $data, $flags, $context);
    }

    public static function sanitizeFilename(string $string): string
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^A-Za-z0-9.\-_]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        $string = preg_replace('/\.+/', '.', $string);
        $string = trim($string, '-.');

        return strtolower($string);
    }

    public static function buildRelativePath(
        string $filePath,
        string $relativeTo
    ): ?string {
        if (str_starts_with($filePath, $relativeTo)) {
            $relativePath = substr($filePath, strlen($relativeTo));
            // Ensure the relative path does not start with a '/'
            return ltrim($relativePath, '/');
        } else {
            return null;
        }
    }
}