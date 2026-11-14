<?php

namespace Wexample\Helpers\Helper;

use function current;

use Exception;

use function explode;
use function floatval;
use function htmlspecialchars_decode;
use function implode;
use function is_file;
use function is_numeric;
use function lcfirst;
use function openssl_encrypt;
use function ord;
use function preg_replace;
use function preg_split;
use function random_int;
use function round;
use function str_replace;
use function strip_tags;
use function strlen;
use function strtolower;
use function strtoupper;

use Random\Engine\Mt19937;
use Random\Randomizer;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;
use Transliterator;

class TextHelper
{
    // Background color
    final public const ASCII_BG_COLOR_GRAY = '40';
    final public const ASCII_BG_COLOR_RED = '41';
    final public const ASCII_BG_COLOR_GREEN = '42';
    final public const ASCII_BG_COLOR_YELLOW = '43';
    final public const ASCII_BG_COLOR_BLUE = '44';
    final public const ASCII_BG_COLOR_MAGENTA = '45';
    final public const ASCII_BG_COLOR_CYAN = '46';
    final public const ASCII_BG_COLOR_WHITE = '47';

    // Darken color
    final public const ASCII_DARK_COLOR_GRAY = '90';
    final public const ASCII_DARK_COLOR_RED = '91';
    final public const ASCII_DARK_COLOR_GREEN = '92';
    final public const ASCII_DARK_COLOR_YELLOW = '93';
    final public const ASCII_DARK_COLOR_BLUE = '94';
    final public const ASCII_DARK_COLOR_MAGENTA = '95';
    final public const ASCII_DARK_COLOR_CYAN = '96';
    final public const ASCII_DARK_COLOR_WHITE = '97';

    // Brighten color
    final public const ASCII_COLOR_GRAY = '30';
    final public const ASCII_COLOR_RED = '31';
    final public const ASCII_COLOR_GREEN = '32';
    final public const ASCII_COLOR_YELLOW = '33';
    final public const ASCII_COLOR_BLUE = '34';
    final public const ASCII_COLOR_MAGENTA = '35';
    final public const ASCII_COLOR_CYAN = '36';
    final public const ASCII_COLOR_WHITE = '37';

    final public const TRIM_MARKER = '...';

    /**
     * Building a slugger or a transliterator compiles its rules: done once per
     * process and reused, since both only depend on the code, never on a request.
     */
    private static ?AsciiSlugger $slugger = null;

    private static ?Transliterator $accentsTransliterator = null;

    public static function removePrefix(
        string $string,
        string $prefix
    ): string {
        return str_starts_with($string, $prefix)
            ? substr($string, strlen($prefix))
            : $string;
    }

    public static function createAbstract(string $string): string
    {
        return static::removeDoubleSpaces(strip_tags($string));
    }

    public static function removeDoubleSpaces(string $string): string
    {
        return preg_replace(
            '/\s+/',
            ' ',
            $string
        );
    }

    public static function htmlToText(string $string): string
    {
        $string = preg_replace(
            '/<br\s?\/?>/iu',
            "\n",
            str_replace(
                "\n",
                '',
                str_replace("\r", '', htmlspecialchars_decode($string))
            )
        );

        return strip_tags($string);
    }

    public static function getNumberHashFromString(string $string): string
    {
        $letter = TextHelper::getLetterFromString($string);

        // Transform to number.
        $first = ord('A');
        $max = ord(strtoupper('Z')) - $first;
        $current = ord(strtoupper($letter)) - $first;

        return 1 / $max * $current;
    }

    /**
     * Returns a single alphanumerical lowercase letter based on a various
     * string. The letter should always be the same for the given string. Used
     * to generate member default color / profile picture.
     */
    public static function getLetterFromString(string $string): string
    {
        // A local engine seeded with 1 shuffles exactly as str_shuffle() did
        // after mt_srand(1), so every string keeps the letter it had, without
        // reseeding the generator the rest of the process draws from.
        $shuffled = (new Randomizer(new Mt19937(1)))->shuffleBytes($string);
        $letters = preg_replace('/[^a-zA-Z]/', '', $shuffled);

        return strtolower($letters[0] ?? '');
    }

    public static function camelToDash(string $string): string
    {
        return strtolower(preg_replace('/([A-Z])/', '-$1', $string));
    }

    public static function stringToKebab(string $string): string
    {
        return strtolower(preg_replace('/_/', '-', $string));
    }

    public static function uniqueFileNameInDir(
        string $dir,
        string $extension
    ): string {
        do {
            // Create file name.
            $fileName = TextHelper::generateSecureId() . '.' . $extension;
        } while (is_file($dir . $fileName));

        return $fileName;
    }

    /**
     * Generate a unique filename.
     * @deprecated Consider using generateSecureId()
     */
    public static function uniqueFileName(): string
    {
        return TextHelper::generateSecureId();
    }

    public static function generateSecureId(
        int $length = 40,
        ?string $prefix = null
    ): string {
        $id = bin2hex(random_bytes($length / 2));

        return $prefix ? $prefix . '_' . $id : $id;
    }

    public static function isNullOrNullString(mixed $value): bool
    {
        if (is_array($value) || is_object($value)) {
            return false;
        }

        return is_null($value) || (is_string($value) && strtolower(trim($value)) === 'null');
    }

    public static function isBoolOrBoolString(mixed $value): bool
    {
        if (is_bool($value)) {
            return true;
        }

        if (is_array($value) || is_object($value)) {
            return false;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));

            return in_array($value, ['true', 'false'], true);
        }

        return false;
    }

    public static function isBooleanOrNull(mixed $value): bool
    {
        if (is_array($value) || is_object($value)) {
            return false;
        }

        if (is_null($value)) {
            return true;
        }

        if (is_bool($value)) {
            return true;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));

            return in_array($value, ['true', 'false', 'null'], true);
        }

        return false;
    }

    public static function parseBooleanOrNull(string|bool|null $bool): ?bool
    {
        if (is_string($bool)) {
            $bool = trim(strtolower($bool));

            if ('true' === $bool) {
                return true;
            }

            if ('false' === $bool) {
                return false;
            }

            // 'null' or any other string value.
            return null;
        }

        return $bool;
    }

    public static function parseBoolean(string|bool|null $bool): bool
    {
        // Turn null to false.
        return (bool) self::parseBooleanOrNull($bool);
    }

    public static function renderBoolean(bool $bool): string
    {
        return $bool ? 'true' : 'false';
    }

    public static function objectToFileName(
        $object,
        string $fieldName = 'title'
    ): string {
        return TextHelper::toAlphaNum(
            ClassHelper::getFieldGetterValue($object, $fieldName)
        );
    }

    public static function toAlphaNum(string $text): string
    {
        return self::slugify($text);
    }

    public static function slugify(string $string): string
    {
        return self::getSlugger()->slug(strtolower($string));
    }

    public static function removeAccents(?string $text): string
    {
        self::$accentsTransliterator ??= Transliterator::createFromRules(
            ':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;',
            Transliterator::FORWARD
        );

        return self::$accentsTransliterator->transliterate($text);
    }

    private static function getSlugger(): AsciiSlugger
    {
        return self::$slugger ??= new AsciiSlugger();
    }

    public static function usernameFromEmail(string $email): string
    {
        return self::slugify(current(explode('@', $email)));
    }

    public static function getIntDataFromString(string $string): int
    {
        return round(self::getFloatFromString($string) * 100);
    }

    public static function getFloatFromString(string $string): ?float
    {
        // Replace commas by points.
        $string = str_replace(',', '.', $string);
        // Keep only numbers, point, and - (negative).
        $string = preg_replace('/[^0-9.-]/', '', $string);

        if (is_numeric($string)) {
            return floatval($string);
        }

        return null;
    }

    /**
     * Check if haystack contain one of given needles.
     */
    public static function hasOne(
        array $needles,
        string $haystack
    ): bool {
        return str_replace($needles, '', $haystack) !== $haystack;
    }

    /**
     * @throws Exception
     */
    public static function generatePassword(int $length): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; ++$i) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public static function splitLines(string $string): array
    {
        return preg_split("/((\r?\n)|(\r\n?))/", $string);
    }

    public static function removeNewLines(string $string): string
    {
        return preg_replace("/(\r\n|\r|\n)/", '', $string);
    }

    public static function toCamel(string $string): string
    {
        return lcfirst(
            static::toClass($string)
        );
    }

    public static function toClass(string $string): string
    {
        return (new UnicodeString(
            static::kebabToDash($string)
        ))->camel()->title();
    }

    public static function kebabToDash(string $string): string
    {
        return str_replace(
            '-',
            '_',
            $string
        );
    }

    public static function removeSuffix(
        string $string,
        string $suffix
    ): string {
        // An empty suffix is guarded: substr($string, 0, -0) would empty the string.
        return '' !== $suffix && str_ends_with($string, $suffix)
            ? substr($string, 0, -strlen($suffix))
            : $string;
    }

    public static function toKebab(string $string): string
    {
        return str_replace(
            '_',
            '-',
            self::toSnake($string)
        );
    }

    public static function toSnake(string $string): string
    {
        return (new UnicodeString($string))->snake();
    }

    public static function emailString(
        string $mail,
        ?string $name = ''
    ): string {
        if ($name) {
            $name = self::getSlugger()->slug($name, ' ');
        }

        return $name . ' <' . $mail . '>';
    }

    public static function isEmail(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    public static function convertUrlsToHyperLinks(string $string): string
    {
        return preg_replace(
            '@(https?://([-\w.]+[-\w])+(:\d+)?(/([\w/_.#-]*(\?\S+)?[^.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $string
        );
    }

    public static function asciiColorWrap(
        string $string,
        string $color = self::ASCII_COLOR_WHITE
    ): string {
        return "\033[1;" . $color . 'm' . $string . "\033[0m";
    }

    public static function alphanumericOnly(string $string): string
    {
        return preg_replace(
            '/[^a-zA-Z0-9]+/',
            '',
            $string
        );
    }

    public static function trimString(
        string $string,
        string $prefix,
        string $suffix,
    ): string {
        return self::removePrefix(
            self::removeSuffix(
                $string,
                $suffix
            ),
            $prefix
        );
    }

    public static function trimExtension(
        string $filePath
    ): string {
        $info = pathinfo($filePath);

        return TextHelper::trimLastChunk(
            $filePath,
            FileHelper::EXTENSION_SEPARATOR . $info['extension']
        );
    }

    public static function trimLastChunk(
        string $string,
        string $separator
    ): string {
        $exp = explode(
            $separator,
            $string
        );

        array_pop($exp);

        return implode(
            $separator,
            $exp
        );
    }

    public static function getFirstChunk(
        string $string,
        string $separator
    ): string {
        $exp = explode(
            $separator,
            $string
        );

        return current($exp);
    }

    public static function getLastChunk(
        string $string,
        string $separator
    ): string {
        $exp = explode(
            $separator,
            $string
        );

        return end($exp);
    }

    public static function trimLastChunkIfMoreThanOne(
        string $string,
        string $separator
    ): string {
        return TextHelper::trimLastChunk(
            $string,
            $separator
        ) ?: $string;
    }

    public static function trimFirstChunkIfMoreThanOne(
        string $string,
        string $separator
    ): string {
        return TextHelper::trimFirstChunk(
            $string,
            $separator
        ) ?: $string;
    }

    public static function trimFirstChunk(
        string $string,
        string $separator
    ): string {
        $exp = explode(
            $separator,
            $string
        );

        array_shift($exp);

        return implode(
            $separator,
            $exp
        );
    }

    public static function convertToBinary(
        mixed $data,
        string $separator = ' '
    ): string {
        if (! is_string($data)) {
            $data = json_encode($data);
        }

        $characters = str_split($data);

        $binary = [];
        foreach ($characters as $character) {
            $data = unpack('H*', $character);
            $binary[] = base_convert($data[1], 16, 2);
        }

        return implode($separator, $binary);
    }

    /**
     * Returns null when the bundle is malformed or the password is wrong.
     *
     * @see https://gist.github.com/nim4n136/7fa38467181130f5a2270c39d495101e
     */
    public static function decrypt(
        string $msgEncryptedBundle,
        string $password
    ): ?string {
        $password = sha1($password);
        $components = explode(':', $msgEncryptedBundle);

        // The IV is 16 characters long: anything else is not a bundle
        // encrypt() made, and openssl would only pad it with a warning.
        if (3 !== count($components) || 16 !== strlen($components[0])) {
            return null;
        }

        $iv = $components[0];
        $salt = hash('sha256', $password . $components[1]);
        $encryptedMsg = $components[2];
        $decryptedMsg = openssl_decrypt(
            $encryptedMsg,
            'aes-256-cbc',
            $salt,
            0,
            $iv
        );

        if (false === $decryptedMsg) {
            return null;
        }

        return $decryptedMsg;
    }

    public static function toString(mixed $value): string
    {
        if (is_object($value)) {
            return '[' . get_class($value) . ']';
        } elseif (is_array($value)) {
            return '[' . gettype($value) . ']';
        } elseif (is_bool($value)) {
            return self::renderBoolean($value);
        }

        return (string) $value;
    }

    /**
     * @see https://gist.github.com/nim4n136/7fa38467181130f5a2270c39d495101e
     */
    public static function encrypt(
        string $string,
        string $password
    ): string {
        // Same shape as before (16 then 40 hex characters, never a ":"), so
        // bundles already stored still decrypt, but drawn from a CSPRNG:
        // mt_rand() is predictable and would make the IV and salt guessable.
        $iv = bin2hex(random_bytes(8));
        $password = sha1($password);
        $salt = bin2hex(random_bytes(20));
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            $string,
            'aes-256-cbc',
            "$saltWithPassword",
            0,
            $iv
        );

        return "$iv:$salt:$encrypted";
    }

    public static function toList(array $array): string
    {
        return PHP_EOL . implode(PHP_EOL, $array) . PHP_EOL;
    }

    public static function inlineSubString(
        string $string,
        int $start,
        int $length
    ): string {
        return substr(TextHelper::removeNewLines($string), $start, $length);
    }
}
