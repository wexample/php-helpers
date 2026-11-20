<?php

namespace Wexample\Helpers\Helper;

use Generator;
use JsonException;
use RuntimeException;

class JsonHelper
{
    /**
     * The items of the JSON array a file holds, decoded one at a time: a file of
     * hundreds of megabytes is read in the memory of its largest item, where
     * json_decode() would need several times the file's size.
     *
     * The file is read by chunks and scanned for the array's own commas — those
     * outside any string and any nested value —; each item between two of them is
     * handed to json_decode().
     *
     * @return Generator<int, mixed> the items, decoded as json_decode() does with `associative` set
     *
     * @throws RuntimeException when the file does not open, holds no array, or ends before its array does
     * @throws JsonException    when an item is no valid JSON
     */
    public static function readArrayItems(
        string $path,
        int $chunkSize = 1048576
    ): Generator {
        $handle = @fopen($path, 'rb');

        if (false === $handle) {
            throw new RuntimeException(sprintf('Unable to open "%s".', $path));
        }

        try {
            $buffer = '';
            // The next byte to read in the buffer, and where the item being read starts in it.
            $offset = 0;
            $start = null;
            $depth = 0;
            $inString = false;

            while (true) {
                if ($offset >= strlen($buffer)) {
                    if (feof($handle)) {
                        throw new RuntimeException(sprintf('"%s" ends before its array does.', $path));
                    }

                    // What is read and outside the current item is let go.
                    $consumed = $start ?? min($offset, strlen($buffer));
                    $buffer = substr($buffer, $consumed).fread($handle, $chunkSize);
                    $offset -= $consumed;
                    $start = null === $start ? null : 0;

                    continue;
                }

                if ($inString) {
                    $offset += strcspn($buffer, '"\\', $offset);

                    if ($offset < strlen($buffer)) {
                        // An escape: the next byte is part of the string, whatever it is.
                        $inString = '\\' === $buffer[$offset];
                        $offset += $inString ? 2 : 1;
                    }

                    continue;
                }

                if (1 === $depth && null === $start) {
                    $offset += strspn($buffer, " \t\r\n", $offset);

                    if ($offset >= strlen($buffer)) {
                        continue;
                    }

                    if (']' !== $buffer[$offset]) {
                        $start = $offset;
                    }
                }

                $offset += strcspn($buffer, '"[]{},', $offset);

                if ($offset >= strlen($buffer)) {
                    continue;
                }

                $char = $buffer[$offset];

                if (0 === $depth && '[' !== $char) {
                    throw new RuntimeException(sprintf('"%s" holds no JSON array.', $path));
                }

                if (1 === $depth && (',' === $char || ']' === $char) && null !== $start) {
                    yield json_decode(substr($buffer, $start, $offset - $start), true, 512, JSON_THROW_ON_ERROR);
                    $start = null;
                }

                if (1 === $depth && ']' === $char) {
                    return;
                }

                match ($char) {
                    '"' => $inString = true,
                    '[', '{' => ++$depth,
                    ']', '}' => --$depth,
                    default => null,
                };

                ++$offset;
            }
        } finally {
            fclose($handle);
        }
    }
}
