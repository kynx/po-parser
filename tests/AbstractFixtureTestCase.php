<?php

declare(strict_types=1);

namespace Sepia\Test;

use PHPUnit\Framework\TestCase;
use Sepia\PoParser\Catalog\Catalog;
use Sepia\PoParser\Parser;

abstract class AbstractFixtureTestCase extends TestCase
{
    protected string $resourcesPath;

    protected function setUp(): void
    {
        $this->resourcesPath = \dirname(__DIR__).'/fixtures/';
    }

    protected function parseFile(string $file): Catalog
    {
        //try {
            return Parser::parseFile($this->resourcesPath.$file);
        //} catch (\Exception $e) {
        //    $this->fail($e->getMessage());
        //}
    }
}
