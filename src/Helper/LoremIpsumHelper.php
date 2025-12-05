<?php

namespace Wexample\Helpers\Helper;

/**
 * Helper for generating placeholder Lorem Ipsum text.
 */
final class LoremIpsumHelper
{
    /**
     * Base Lorem Ipsum sentence that always starts the text
     */
    private const BASE_START = 'Lorem ipsum';

    /**
     * Collection of Lorem Ipsum sentences to randomize
     */
    private const LOREM_SENTENCES = [
        'dolor sit amet, consectetur adipiscing elit.',
        'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
        'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.',
        'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia.',
        'deserunt mollit anim id est laborum.',
        'Sed ut perspiciatis unde omnis iste natus error sit voluptatem.',
        'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.',
        'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.',
    ];

    /**
     * Generate placeholder text of a given length (in characters).
     * Text will start with "Lorem ipsum" and continue with random Lorem Ipsum sentences.
     *
     * @param int $length Length in characters. Must be >= 0.
     * @return string Lorem Ipsum text truncated or padded to length.
     */
    public static function generate(int $length = 100): string
    {
        if ($length <= 0) {
            return '';
        }

        // Always start with "Lorem ipsum"
        $text = self::BASE_START . ' ';
        $currentLength = mb_strlen($text, 'UTF-8');

        // Add random sentences until we reach desired length
        while ($currentLength < $length) {
            $sentence = self::LOREM_SENTENCES[array_rand(self::LOREM_SENTENCES)];
            $text .= $sentence . ' ';
            $currentLength = mb_strlen($text, 'UTF-8');
        }

        return mb_substr(rtrim($text), 0, $length, 'UTF-8');
    }
}
