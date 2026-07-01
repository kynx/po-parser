<?php

declare(strict_types=1);

namespace Sepia\Test\UnitTest;

use Exception;
use PHPUnit\Framework\TestCase;
use Sepia\PoParser\Catalog\Catalog;
use Sepia\PoParser\Catalog\Header;
use Sepia\PoParser\Parser;
use Sepia\PoParser\SourceHandler\StringSource;

class ParserTest extends TestCase
{
    public function testShouldParseHeaders(): void
    {
        $doc     =
        'msgid ""
        msgstr ""
        "Project-Id-Version: value 1\n"
        "Report-Msgid-Bugs-To: value 2\n"
        
        msgid "string.1"
        msgstr "translation.1"
        ';
        $catalog = $this->parse($doc);

        $expectedHeaders = new Header([
            'Project-Id-Version: value 1',
            'Report-Msgid-Bugs-To: value 2',
        ]);
        $this->assertEquals(
            $expectedHeaders,
            $catalog->getHeader()
        );
    }

    /**
     * @throws Exception
     */
    public function parse(string $doc): Catalog
    {
        $parser = new Parser(new StringSource($doc));
        return $parser->parse();
    }
}
