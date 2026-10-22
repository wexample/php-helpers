<?php

namespace Wexample\Helpers\Class\Traits;

trait HasUniqueId
{
    protected string $uniqueId;

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * @param string $id
     */
    public function setUniqueId(string $id): void
    {
        $this->uniqueId = $id;
    }

    public function generateUniqueId(string $prefix = ''): string
    {
        $id = bin2hex(random_bytes(8)); // 16 chars

        $this->uniqueId = $prefix !== '' ? rtrim($prefix, '-') . '-' . $id : $id;

        return $this->uniqueId;
    }
}
