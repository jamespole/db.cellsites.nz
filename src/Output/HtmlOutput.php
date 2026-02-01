<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

abstract class HtmlOutput extends Output
{
    private string $title;
    public function __construct()
    {
        parent::__construct('text/html');
    }
    final protected function generate(): string
    {
        $string = '<!doctype html>' . PHP_EOL;
        $string .= '<html lang="en">' . PHP_EOL;
        $string .= '<head>' . PHP_EOL;
        $string .= '<meta charset="utf-8">' . PHP_EOL;
        $string .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $string .= sprintf(
            '<title>%s</title>' . PHP_EOL,
            htmlentities($this->getTitle())
        );
        $string .= $this->generateHead();
        $string .= '</head>' . PHP_EOL;
        $string .= '<body>' . PHP_EOL;
        $string .= $this->generateBody();
        $string .= '</body>' . PHP_EOL;
        $string .= '</html>' . PHP_EOL;
        return($string);
    }
    private function getTitle(): string
    {
        return($this->title);
    }
    abstract protected function generateBody(): string;
    abstract protected function generateHead(): string;
    final protected function setTitle(string $title)
    {
        $this->title = $title;
    }
}
