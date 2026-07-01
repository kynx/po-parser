<?php

declare(strict_types=1);

namespace Sepia\Test;

use Sepia\PoParser\Catalog\Entry;

class EntryBuilder
{
    public function build(): Entry
    {
        $entry = new Entry(
            $this->msgId,
            $this->msgStr
        );
        if ($this->msgPluralId) {
            $entry->setMsgIdPlural($this->msgPluralId);
        }
        if ($this->context) {
            $entry->setMsgCtxt($this->context);
        }
        if ($this->reference) {
            $entry->setReference($this->reference);
        }
        if ($this->translatorComments) {
            $entry->setTranslatorComments($this->translatorComments);
        }
        if ($this->developerComments) {
            $entry->setDeveloperComments($this->developerComments);
        }
        $entry->setFlags($this->flags);
        $entry->setPreviousEntry($this->previousEntry);
        $entry->setObsolete($this->obsolete);
        $entry->setMsgStrPlurals($this->pluralTranslations);

        return $entry;
    }

    private string $msgId;
    private ?string $msgPluralId;
    private ?string $msgStr;
    private ?string $context;
    /** @var array<string> */
    private array $reference;
    /** @var array<string> */
    private array $translatorComments;
    /** @var array<string> */
    private array $developerComments;
    /** @var array<string> */
    private array $flags;
    private ?Entry $previousEntry;
    private bool $obsolete;
    /** @var array<string> */
    private array $pluralTranslations;

    public function __construct()
    {
        $this->msgPluralId = null;
        $this->context = null;
        $this->reference = [];
        $this->translatorComments = [];
        $this->developerComments = [];
        $this->flags = [];
        $this->previousEntry = null;
        $this->obsolete = false;
        $this->pluralTranslations = [];
    }

    public static function anEntry(): self
    {
        $builder = new EntryBuilder();
        $builder
            ->withId('an-id')
            ->withTranslation('a translation');

        return $builder;
    }

    public function withId(string $id): self
    {
        $this->msgId = $id;
        return $this;
    }

    public function withPluralId(string $id): self
    {
        $this->msgPluralId = $id;
        return $this;
    }

    public function withTranslation(string $msgstr): self
    {
        $this->msgStr = $msgstr;
        return $this;
    }

    public function withContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param array<string> $reference
     */
    public function withReference(array $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @param array<string> $comments
     */
    public function withTranslatorComment(array $comments): self
    {
        $this->translatorComments = $comments;
        return $this;
    }

    /**
     * @param array<string> $comments
     */
    public function withDeveloperComment(array $comments): self
    {
        $this->developerComments = $comments;
        return $this;
    }

    /**
     * @param array<string> $flags
     */
    public function withFlags(array $flags): self
    {
        $this->flags = $flags;
        return $this;
    }

    public function withPreviousEntry(?Entry $entry): self
    {
        $this->previousEntry = $entry;
        return $this;
    }

    public function obsolete(): self
    {
        $this->obsolete = true;
        return $this;
    }

    public function withPluralTranslation(int $numeral, string $translation): self
    {
        $this->pluralTranslations[$numeral] = $translation;
        return $this;
    }
}