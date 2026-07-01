<?php

declare(strict_types=1);

namespace Sepia\PoParser\Catalog;

use function md5;

class CatalogArray implements Catalog
{
    /** @var array<string, Entry> */
    protected array $entries;
    protected Header $headers;

    /**
     * @param array<Entry> $entries
     */
    public function __construct(array $entries = [])
    {
        $this->headers = new Header();
        $this->entries = [];

        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
    }

    public function addEntry(Entry $entry): void
    {
        $key                 = $this->getEntryHash(
            $entry->getMsgId(),
            $entry->getMsgCtxt()
        );
        $this->entries[$key] = $entry;
    }

    public function addHeaders(Header $headers): void
    {
        $this->headers = $headers;
    }

    public function removeEntry(string $msgid, ?string $msgctxt = null): void
    {
        $key = $this->getEntryHash($msgid, $msgctxt);
        if (isset($this->entries[$key])) {
            unset($this->entries[$key]);
        }
    }

    /**
     * @return array<string>
     */
    public function getHeaders(): array
    {
        return $this->headers->asArray();
    }

    public function getHeader(): Header
    {
        return $this->headers;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getEntry(string $msgId, ?string $context = null): ?Entry
    {
        $key = $this->getEntryHash($msgId, $context);
        if (! isset($this->entries[$key])) {
            return null;
        }

        return $this->entries[$key];
    }

    private function getEntryHash(string $msgId, ?string $context = null): string
    {
        return md5($msgId . $context);
    }
}
