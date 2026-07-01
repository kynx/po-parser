<?php

declare(strict_types=1);

namespace Sepia\Test\UnitTest;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sepia\PoParser\Catalog\CatalogArray;
use Sepia\PoParser\PoCompiler;
use Sepia\Test\EntryBuilder;

use function mb_internal_encoding;

class PoCompilerTest extends TestCase
{
    public function testShouldCompileSingleLineTranslation(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withTranslation('hello fellow ant')
                ->withContext('context 1')
                ->withReference(['src/views/forms.php:44'])
                ->withTranslatorComment(['translator comment'])
                ->withDeveloperComment(['developer comment'])
                ->withFlags(['1', '2', '3'])
                ->withPreviousEntry(
                    EntryBuilder::anEntry()
                        ->withId('previous.string.1')
                        ->withContext('previous context')
                        ->build()
                )
                ->build(),
            EntryBuilder::anEntry()
                ->withId('second message')
                ->withTranslation('segón missatge')
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<POFILE
            #| msgid "previous.string.1"
            # translator comment
            #. developer comment
            #: src/views/forms.php:44
            #, 1, 2, 3
            msgctxt "context 1"
            msgid "a-message"
            msgstr "hello fellow ant"
            
            msgid "second message"
            msgstr "segón missatge"
            
            POFILE,
            $output
        );
    }

    public function testShouldCompileObsoleteTranslation(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withTranslation('hello fellow ant')
                ->obsolete()
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<POFILE
            #~ msgid "a-message"
            #~ msgstr "hello fellow ant"
            
            POFILE,
            $output
        );
    }

    public function testShouldCompileMultipleLineTranslation(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withTranslation('hello fellow ant')
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<POFILE
            msgid "a-message"
            msgstr "hello fellow ant"
            
            POFILE,
            $output
        );
    }

    public function testShouldCompileTranslationWithPlurals(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withPluralId('a-message %d')
                ->withTranslation('hello fellow ant')
                ->withPluralTranslation(0, 'translation plural 0')
                ->withPluralTranslation(1, 'translation plural 1')
                ->withPluralTranslation(2, 'translation plural 2')
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<POFILE
            msgid "a-message"
            msgid_plural "a-message %d"
            msgstr[0] "translation plural 0"
            msgstr[1] "translation plural 1"
            msgstr[2] "translation plural 2"
            
            POFILE,
            $output
        );
    }

    public function testShouldCompileObsoletePlurals(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withPluralId('%d obsolete strings')
                ->withTranslation('hello fellow ant')
                ->withPluralTranslation(0, 'translation plural 0')
                ->withPluralTranslation(1, 'translation plural 1')
                ->withPluralTranslation(2, 'translation plural 2')
                ->obsolete()
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<POFILE
            #~ msgid "a-message"
            #~ msgid_plural "%d obsolete strings"
            #~ msgstr[0] "translation plural 0"
            #~ msgstr[1] "translation plural 1"
            #~ msgstr[2] "translation plural 2"
            
            POFILE,
            $output
        );
    }

    public function testShouldCompileEscapingSpecialChars(): void
    {
        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a\"b\"c')
                ->withTranslation('quotes')
                ->build(),
            EntryBuilder::anEntry()
                ->withId('a\nb\nc')
                ->withTranslation('slashes')
                ->build(),
            EntryBuilder::anEntry()
                ->withId("a\nb\nc")
                ->withTranslation("proper\nlinebreaks")
                ->build(),
        ]);

        $compiler = new PoCompiler();
        $output   = $compiler->compile($catalog);

        $this->assertEquals(
            <<<EXPECTED
            msgid "a\\\\\"b\\\\\"c"
            msgstr "quotes"
            
            msgid "a\\\\nb\\\\nc"
            msgstr "slashes"
            
            msgid "a\\nb\\nc"
            msgstr "proper\\nlinebreaks"
            
            EXPECTED,
            $output
        );
    }

    /**
     * @param array<string> $assert
     */
    #[DataProvider('wrappingDataProvider')]
    public function testShouldCompileTranslationWithWrappingLongLines(
        string $value,
        int $wrappingColumn,
        bool $shouldWrapLines,
        array $assert
    ): void {
        // Make sure that encoding is set to UTF-8 for this test
        mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        $catalog = new CatalogArray([
            EntryBuilder::anEntry()
                ->withId('a-message')
                ->withTranslation($value)
                ->build(),
        ]);

        $compiler = new PoCompiler($wrappingColumn);
        $output   = $compiler->compile($catalog);

        $expected = 'msgid "a-message"' . "\n";
        if ($shouldWrapLines) {
            $expected .= 'msgstr ""' . "\n";
        } else {
            $expected .= 'msgstr ';
        }
        foreach ($assert as $line) {
            $expected .= '"' . $line . '"' . "\n";
        }

        $this->assertEquals($expected, $output);
    }

    /**
     * @return array<string, array{value: string, wrappingColumn: int, shouldWrapLines: bool, assert: array<string>}>
     */
    public static function wrappingDataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'Multibyte Wrap (char 81)'          => [
                'value'           => 'Hello everybody, Hello ladies and gentlemen.... this is a multibyte translation á with a multibyte beginning at char 81.',
                'wrappingColumn'  => 80,
                'shouldWrapLines' => true,
                'assert'          => [
                    'Hello everybody, Hello ladies and gentlemen.... this is a multibyte translation ',
                    'á with a multibyte beginning at char 81.',
                ],
            ],
            'Multibyte Wrap (char 80)'          => [
                'value'           => 'Hello everybody, Hello ladies and gentlemen... this is a multibyte translation á with a multibyte beginning at char 80.',
                'wrappingColumn'  => 80,
                'shouldWrapLines' => true,
                'assert'          => [
                    'Hello everybody, Hello ladies and gentlemen... this is a multibyte translation á',
                    ' with a multibyte beginning at char 80.',
                ],
            ],
            'Multibyte Wrap (char 79)'          => [
                'value'           => 'Hello everybody, Hello ladies and gentlemen.. this is a multibyte translation á with multibytes beginning at char 79.',
                'wrappingColumn'  => 80,
                'shouldWrapLines' => true,
                'assert'          => [
                    'Hello everybody, Hello ladies and gentlemen.. this is a multibyte translation á ',
                    'with multibytes beginning at char 79.',
                ],
            ],
            'Escape-Sequence Wrap (char 80+81)' => [
                'value'           => 'Hello everybody, Hello ladies and gentlemen..... this is a line with more than \"eighty\" chars. And char 80+81 is an escaped double quote.',
                'wrappingColumn'  => 80,
                'shouldWrapLines' => true,
                'assert'          => [
                    'Hello everybody, Hello ladies and gentlemen..... this is a line with more than ',
                    '\\\\\"eighty\\\\\" chars. And char 80+81 is an escaped double quote.',
                ],
            ],
            'Escape-Sequence Wrap (char 79+80)' => [
                'value'           => 'Hello everybody, Hello ladies and gentlemen.... this is a line with more than \"eighty\" chars. And char 79+80 is an escaped double quote.',
                'wrappingColumn'  => 80,
                'shouldWrapLines' => true,
                'assert'          => [
                    'Hello everybody, Hello ladies and gentlemen.... this is a line with more than ',
                    '\\\\\"eighty\\\\\" chars. And char 79+80 is an escaped double quote.',
                ],
            ],
            'String with a lot of multibyte characters should not break when wrappingColumn is at its mb_strlen' => [
                'value'           => 'kategóriáját kötelező',
                'wrappingColumn'  => 21,
                'shouldWrapLines' => false,
                'assert'          => [
                    'kategóriáját kötelező',
                ],
            ],
        ];
        // phpcs:enable
    }
}
