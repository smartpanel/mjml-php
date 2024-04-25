<?php

namespace SmartPanel\Mjml;

class MjmlResult
{
    protected array $rawResult;

    public function __construct(array $rawResult)
    {
        $this->rawResult = $rawResult;
    }

    public function html(): string
    {
        return $this->rawResult['html'] ?? '';
    }

    public function array(): array
    {
        return $this->rawResult['json'] ?? [];
    }

    public function raw(): array
    {
        return $this->rawResult;
    }

    public function hasErrors(): bool
    {
        return count($this->errors()) > 0;
    }

    /** @return array<MjmlError> */
    public function errors(): array
    {
        return array_map(function (array $errorProperties) {
            return new MjmlError($errorProperties);
        }, $this->rawResult['errors'] ?? []);
    }
}
