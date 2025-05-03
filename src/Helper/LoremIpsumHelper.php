<?php

namespace Wexample\Helpers\Helper;

/**
 * Helper for generating placeholder Lorem Ipsum text.
 */
final class LoremIpsumHelper
{
    /**
     * Base Lorem Ipsum sentence
     */
    private const BASE_LOREM_IPSUM =
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, ' .
        'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    /**
     * Generate placeholder text of a given length (in characters).
     *
     * @param int $length Length in characters. Must be >= 0.
     * @return string Lorem Ipsum text truncated or padded to length.
     */
    public static function generate(int $length = 100): string
    {
        if ($length <= 0) {
            return '';
        }

        $baseLength = mb_strlen(self::BASE_LOREM_IPSUM, 'UTF-8');
        $repeats = (int) ceil($length / $baseLength);
        $text = str_repeat(self::BASE_LOREM_IPSUM . ' ', $repeats);

        return mb_substr(rtrim($text), 0, $length, 'UTF-8');
    }
}