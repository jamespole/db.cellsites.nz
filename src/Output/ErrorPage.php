<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

final class ErrorPage extends Page
{
    private int $error;
    public function __construct(int $error)
    {
        parent::__construct();
        $this->error = $error;
    }
    protected function generateContent(): string
    {
        $this->setResponseCode($this->error);
        $string = '<h2>Error ' . $this->error . '</h2>' . PHP_EOL;
        if (isset($_SERVER['REQUEST_URI'])) {
            $string .= '<p><b>REQUEST_URI:</b> ' . $_SERVER['REQUEST_URI'] . '</p>' . PHP_EOL;
        }
        return($string);
    }
}
