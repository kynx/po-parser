<?php

declare(strict_types=1);

namespace Sepia\PoParser\Catalog;

class Entry
{
    protected string $msgId;

    protected ?string $msgStr;

    protected ?string $msgIdPlural;

    protected array $msgStrPlurals;

    protected ?string $msgCtxt;

    protected ?Entry $previousEntry;

    protected ?bool $obsolete;

    protected array $flags;

    protected array $translatorComments;

    protected array $developerComments;

    protected array $reference;

    public function __construct(string $msgId, ?string $msgStr = null)
    {
        $this->msgId = $msgId;
        $this->msgStr = $msgStr;
        $this->msgIdPlural = null;
        $this->msgStrPlurals = [];
        $this->msgCtxt = null;
        $this->previousEntry = null;
        $this->obsolete = null;
        $this->flags = [];
        $this->translatorComments = [];
        $this->developerComments = [];
        $this->reference = [];
    }

    public function setMsgId(string $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function setMsgStr(?string $msgStr): self
    {
        $this->msgStr = $msgStr;

        return $this;
    }

    public function setMsgIdPlural(?string $msgIdPlural): self
    {
        $this->msgIdPlural = $msgIdPlural;

        return $this;
    }

    public function setMsgCtxt(?string $msgCtxt): self
    {
        $this->msgCtxt = $msgCtxt;

        return $this;
    }

    public function setPreviousEntry(?Entry $previousEntry): self
    {
        $this->previousEntry = $previousEntry;

        return $this;
    }

    public function setObsolete(?bool $obsolete): self
    {
        $this->obsolete = $obsolete;

        return $this;
    }

    public function setFlags(array $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    public function setTranslatorComments(array $translatorComments): self
    {
        $this->translatorComments = $translatorComments;

        return $this;
    }

    public function setDeveloperComments(array $developerComments): self
    {
        $this->developerComments = $developerComments;

        return $this;
    }

    public function setReference(array $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function setMsgStrPlurals(array $msgStrPlurals): self
    {
        $this->msgStrPlurals = $msgStrPlurals;

        return $this;
    }

    public function getMsgId(): string
    {
        return $this->msgId;
    }

    public function getMsgStr(): ?string
    {
        return $this->msgStr;
    }

    public function getMsgIdPlural(): ?string
    {
        return $this->msgIdPlural;
    }

    public function getMsgCtxt(): ?string
    {
        return $this->msgCtxt;
    }

    public function getPreviousEntry(): ?Entry
    {
        return $this->previousEntry;
    }

    public function isObsolete(): bool
    {
        return $this->obsolete === true;
    }

    public function isFuzzy(): bool
    {
        return \in_array('fuzzy', $this->getFlags(), true);
    }

    public function isPlural(): bool
    {
        return $this->getMsgIdPlural() !== null || \count($this->getMsgStrPlurals()) > 0;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getTranslatorComments(): array
    {
        return $this->translatorComments;
    }

    public function getDeveloperComments(): array
    {
        return $this->developerComments;
    }

    public function getReference(): array
    {
        return $this->reference;
    }

    /**
     * @return string[]
     */
    public function getMsgStrPlurals(): array
    {
        return $this->msgStrPlurals;
    }
}
