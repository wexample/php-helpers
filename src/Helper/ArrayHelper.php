<?php

namespace Wexample\Helpers\Helper;

class ArrayHelper
{
    /**
     * Determines if an array is associative.
     *
     * @param array $array The array to check.
     *
     * @return bool True if associative, false if sequential.
     */
    public static function isAssociative(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Recursively canonicalizes an array by sorting its keys or values.
     *
     * Associative arrays are sorted by key.
     * Sequential arrays are sorted by comparing their string representations.
     *
     * @param array $array The array to canonicalize.
     *
     * @return array The canonicalized array.
     */
    public static function canonicalize(array $array): array
    {
        // Recursively canonicalize each element.
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = static::canonicalize($value);
            }
        }

        if (static::isAssociative($array)) {
            ksort($array);
        } else {
            // For sequential arrays, sort by the string representation.
            usort($array, function (
                $a,
                $b
            ) {
                return strcmp(var_export($a, true), var_export($b, true));
            });
        }

        return $array;
    }
}