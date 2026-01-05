<?php

namespace Wexample\Helpers\Traits;

trait WithDomId
{
    protected string $domId;

    public function getDomId(): string
    {
        return $this->domId;
    }

    public function setDomId(string $domId): void
    {
        $this->domId = $domId;
    }
}
