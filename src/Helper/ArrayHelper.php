<?php

namespace Wexample\Helpers\Helper;

class ArrayHelper
{
    const string PATH_SEPARATOR_DEFAULT = '.';

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

    /**
     * Recursively compares two values (arrays or scalars) and returns true if they are identical,
     * false otherwise.
     *
     * When comparing arrays, if a key is missing in one of them while the corresponding value
     * in the other is an empty array, this difference is ignored when $allowEmptyMissing is true.
     *
     * @param mixed $expected The expected value (can be an array or a scalar).
     * @param mixed $actual The actual value (can be an array or a scalar).
     * @param bool $allowEmptyMissing If true, a missing key paired with an empty array is ignored.
     *
     * @return bool True if $expected and $actual are identical, false otherwise.
     */
    public static function areSame(
        mixed $expected,
        mixed $actual,
        bool $allowEmptyMissing = false
    ): bool
    {
        if (is_array($expected) && is_array($actual)) {
            // Compare expected keys with actual values.
            foreach ($expected as $key => $expectedValue) {
                if (!array_key_exists($key, $actual)) {
                    // If allowed, ignore the missing key if the expected value is an empty array.
                    if (!($allowEmptyMissing && is_array($expectedValue) && empty($expectedValue))) {
                        return false;
                    }
                } else {
                    if (!self::areSame($expectedValue, $actual[$key], $allowEmptyMissing)) {
                        return false;
                    }
                }
            }
            // Check for extra keys present in $actual but not in $expected.
            foreach ($actual as $key => $actualValue) {
                if (!array_key_exists($key, $expected)) {
                    // If allowed, ignore the extra key if the actual value is an empty array.
                    if (!($allowEmptyMissing && is_array($actualValue) && empty($actualValue))) {
                        return false;
                    }
                }
            }
            return true;
        } else {
            // For scalars or differing types, perform a strict comparison.
            return $expected === $actual;
        }
    }

    /**
     * Normalizes two arrays for diff comparison by aligning empty-array properties.
     *
     * If $allowEmptyMissing is true, then for any key that is missing in one array but exists in the other
     * as an empty array, the missing key is added with an empty array. This alignment is performed recursively.
     *
     * @param array &$a The first array.
     * @param array &$b The second array.
     * @param bool $allowEmptyMissing If true, empty-array keys are aligned between the two arrays.
     *
     * @return array An array containing the normalized versions of $a and $b as [$normalizedA, $normalizedB].
     */
    public static function normalize(
        array &$a,
        array &$b,
        bool $allowEmptyMissing = false
    ): array
    {
        // If not allowing empty missing, nothing to normalize.
        if ($allowEmptyMissing) {

            // Normalize $a based on $b:
            foreach ($b as $key => $value) {
                if (!array_key_exists($key, $a)) {
                    // If $b has an empty array at this key and $a is missing it, add it.
                    if (is_array($value) && empty($value)) {
                        $a[$key] = [];
                    }
                } else {
                    // If both arrays have the key and both values are arrays, normalize them recursively.
                    if (is_array($value) && is_array($a[$key])) {
                        list($a[$key], $b[$key]) = self::normalize($a[$key], $value, $allowEmptyMissing);
                    }
                }
            }

            // Normalize $b based on $a:
            foreach ($a as $key => $value) {
                if (!array_key_exists($key, $b)) {
                    // If $a has an empty array at this key and $b is missing it, add it.
                    if (is_array($value) && empty($value)) {
                        $b[$key] = [];
                    }
                }
            }
        }

        // Sort orders.
        $a = ArrayHelper::canonicalize($a);
        $b = ArrayHelper::canonicalize($b);

        return [$a, $b];
    }

    public static function getItemByPath(
        array $data,
        string|array $key,
        mixed $default = null,
        string $separator = self::PATH_SEPARATOR_DEFAULT
    ): mixed
    {
        if (is_string($key)) {
            $keys = explode($separator, $key);
        }

        foreach ($keys as $k) {
            if (is_array($data) && array_key_exists($k, $data)) {
                $data = $data[$k];
            } else {
                return $default;
            }
        }

        return $data;
    }

    public static function flattenArray(
        array $array,
        string $prefix = ''
    ): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {
                $result = array_merge($result, static::flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}