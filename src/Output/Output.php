<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

abstract class Output
{
    private string $contentType;
    private int $responseCode = 200;
    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }
    final public function output(): void
    {
        $output = $this->generate();
        http_response_code($this->responseCode);
        header(sprintf('Content-Type: %s; charset=UTF-8', $this->contentType));
        echo $output;
    }
    abstract protected function generate(): string;
    protected function setResponseCode(int $responseCode): void
    {
        $this->responseCode = $responseCode;
    }
}
