<?php

declare(strict_types=1);

namespace Sepia\PoParser\Catalog;

class Header
{
    protected array $headers;

    protected ?int $nPlurals;

    public function __construct(array $headers = [])
    {
        $this->setHeaders($headers);
        $this->nPlurals = null;
    }

    public function getPluralFormsCount(): int
    {
        if ($this->nPlurals !== null) {
            return $this->nPlurals;
        }

        $header = $this->getHeaderValue('Plural-Forms');
        if ($header === null) {
            $this->nPlurals = 0;
            return $this->nPlurals;
        }

        $matches = [];
        if (\preg_match('/nplurals=([0-9]+)/', $header, $matches) !== 1) {
            $this->nPlurals = 0;
            return $this->nPlurals;
        }

        $this->nPlurals = isset($matches[1]) ? (int)$matches[1] : 0;

        return $this->nPlurals;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function asArray(): array
    {
        return $this->headers;
    }

    protected function getHeaderValue(string $headerName): ?string
    {
        $header = \array_values(\array_filter(
            $this->headers,
            function ($string) use ($headerName) {
                return \preg_match('/' . $headerName . ':(.*)/i', $string) === 1;
            }
        ));

        return \count($header) ? $header[0] : null;
    }
}
