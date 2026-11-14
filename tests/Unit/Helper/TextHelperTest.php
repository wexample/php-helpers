<?php

declare(strict_types=1);

namespace Wexample\Helpers\Tests\Unit\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wexample\Helpers\Helper\TextHelper;

class TextHelperTest extends TestCase
{
    public function testToKebab(): void
    {
        $this->assertSame('some-thing-in-class-case', TextHelper::toKebab('Some_ThingInClassCase'));
        $this->assertSame('already-kebab', TextHelper::toKebab('already-kebab'));
    }

    public function testToClassAndCamel(): void
    {
        $this->assertSame('SomeThing', TextHelper::toClass('some-thing'));
        $this->assertSame('SomeThing', TextHelper::toClass('some_thing'));
        $this->assertSame('someThing', TextHelper::toCamel('some-thing'));
    }

    public function testChunks(): void
    {
        $this->assertSame('a.b', TextHelper::trimLastChunk('a.b.c', '.'));
        $this->assertSame('b.c', TextHelper::trimFirstChunk('a.b.c', '.'));
        $this->assertSame('a', TextHelper::getFirstChunk('a.b.c', '.'));
        $this->assertSame('c', TextHelper::getLastChunk('a.b.c', '.'));
        $this->assertSame('abc', TextHelper::trimLastChunkIfMoreThanOne('abc', '.'));
        $this->assertSame('abc', TextHelper::trimFirstChunkIfMoreThanOne('abc', '.'));
        $this->assertSame('file', TextHelper::trimExtension('file.txt'));
    }

    public function testPrefixAndSuffixAreRemovedOnlyAtTheirEnd(): void
    {
        $this->assertSame('name', TextHelper::removePrefix('prefix.name', 'prefix.'));
        $this->assertSame('a-prefix.name', TextHelper::removePrefix('a-prefix.name', 'prefix.'));
        $this->assertSame('name', TextHelper::removePrefix('name', ''));
        $this->assertSame('User', TextHelper::removeSuffix('UserController', 'Controller'));
        $this->assertSame('ControllerUser', TextHelper::removeSuffix('ControllerUser', 'Controller'));
        $this->assertSame('name', TextHelper::removeSuffix('name', ''));
        $this->assertSame('b', TextHelper::trimString('[b]', '[', ']'));
    }

    #[DataProvider('booleanOrNullProvider')]
    public function testParseBooleanOrNull(string|bool|null $value, ?bool $expected): void
    {
        $this->assertSame($expected, TextHelper::parseBooleanOrNull($value));
    }

    public static function booleanOrNullProvider(): iterable
    {
        yield 'true string' => [' TRUE ', true];
        yield 'false string' => ['false', false];
        yield 'null string' => ['null', null];
        yield 'any other string' => ['yes', null];
        yield 'bool' => [false, false];
        yield 'null' => [null, null];
    }

    public function testGetFloatFromString(): void
    {
        $this->assertSame(1234.5, TextHelper::getFloatFromString('1 234,5 €'));
        $this->assertSame(-3.0, TextHelper::getFloatFromString('-3'));
        $this->assertNull(TextHelper::getFloatFromString('abc'));
    }

    public function testLetterFromStringIsTheOneMtSrandGave(): void
    {
        foreach (['Jean Dupont', 'alice@example.com', 'X', '42 rue B'] as $string) {
            $this->assertSame(
                $this->legacyLetterFromString($string),
                TextHelper::getLetterFromString($string),
                $string
            );
        }

        $this->assertSame('', TextHelper::getLetterFromString('1234'));
    }

    public function testLetterFromStringLeavesTheGlobalGeneratorAlone(): void
    {
        mt_srand(42);
        $expected = mt_rand();

        mt_srand(42);
        TextHelper::getLetterFromString('Jean Dupont');

        $this->assertSame($expected, mt_rand());

        mt_srand();
    }

    public function testEncryptedTextDecryptsWithItsPassword(): void
    {
        $bundle = TextHelper::encrypt('secret message', 'password');

        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}:[0-9a-f]{40}:/', $bundle);
        $this->assertSame('secret message', TextHelper::decrypt($bundle, 'password'));
        $this->assertNotSame($bundle, TextHelper::encrypt('secret message', 'password'));
    }

    public function testBundlesOfTheFormerShapeStillDecrypt(): void
    {
        // Built as encrypt() used to, with sha1(mt_rand()) for the IV and salt.
        $iv = substr(sha1('1'), 0, 16);
        $salt = sha1('2');
        $encrypted = openssl_encrypt(
            'secret message',
            'aes-256-cbc',
            hash('sha256', sha1('password') . $salt),
            0,
            $iv
        );

        $this->assertSame('secret message', TextHelper::decrypt("$iv:$salt:$encrypted", 'password'));
    }

    public function testMalformedBundleDecryptsToNull(): void
    {
        $this->assertNull(TextHelper::decrypt('not a bundle', 'password'));
        $this->assertNull(TextHelper::decrypt('a:b:c', 'password'));
    }

    public function testRemoveAccents(): void
    {
        $this->assertSame('elephant', TextHelper::removeAccents('Éléphant'));
        $this->assertSame('ecole', TextHelper::removeAccents('École'));
    }

    public function testSlugify(): void
    {
        // AsciiSlugger needs symfony/translation-contracts, which the
        // applications bring but this package does not require.
        if (! interface_exists(\Symfony\Contracts\Translation\LocaleAwareInterface::class)) {
            $this->markTestSkipped('symfony/translation-contracts is not installed.');
        }

        $this->assertSame('hello-world-e', TextHelper::slugify('Hello World é'));
        $this->assertSame('hello-world-e', TextHelper::slugify('Hello World é'));
        $this->assertSame('Jean Dupont <a@example.com>', TextHelper::emailString('a@example.com', 'Jean Dupont'));
    }

    /**
     * What getLetterFromString() did before it stopped reseeding mt_rand().
     */
    private function legacyLetterFromString(string $string): string
    {
        mt_srand(1);
        $letter = strtolower(preg_replace('/[^a-zA-Z]/', '', str_shuffle($string))[0]);
        mt_srand();

        return $letter;
    }
}
