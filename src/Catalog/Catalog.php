<?php

declare(strict_types=1);

namespace Sepia\PoParser\Catalog;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface Catalog
{
    public function addEntry(Entry $entry): void;

    public function addHeaders(Header $headers): void;

    public function removeEntry(string $msgid, ?string $msgctxt = null): void;

    /**
     * @return array<string>
     */
    public function getHeaders(): array;

    public function getHeader(): Header;

    /**
     * @return array<string, Entry>
     */
    public function getEntries(): array;

    public function getEntry(string $msgId, ?string $context = null): ?Entry;
}
