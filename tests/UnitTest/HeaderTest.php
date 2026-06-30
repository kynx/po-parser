<?php

declare(strict_types=1);

namespace Sepia\Test\UnitTest;

use PHPUnit\Framework\TestCase;
use Sepia\PoParser\Catalog\Catalog;
use Sepia\PoParser\Parser;

class HeaderTest extends TestCase
{
    public function testGetPluralFormsCount(): void
    {
        $catalog = $this->parseFile();

        $this->assertEquals(3, $catalog->getHeader()->getPluralFormsCount());
    }

    protected function parseFile(): Catalog
    {
        return Parser::parseFile(\dirname(\dirname(__DIR__)).'/fixtures/basicHeadersMultiline.po');
    }
}
