<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

abstract class Page extends Output
{
    private bool $requiresLeaflet = false;
    public function __construct()
    {
        parent::__construct('text/html');
    }
    protected function generate(): string
    {
        $string = '<!DOCTYPE html>' . PHP_EOL;
        $string .= '<html lang="en">' . PHP_EOL;
        $string .= '<head>' . PHP_EOL;
        $string .= '<meta charset="UTF-8">' . PHP_EOL;
        $string .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $string .= '<title>Cell Sites Database</title>' . PHP_EOL;
        $string .= '<link rel="stylesheet" href="/style.css">' . PHP_EOL;
        if ($this->requiresLeaflet === true) {
            $string .= '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>' . PHP_EOL;
            $string .= '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>' . PHP_EOL;
            $string .= '<link rel="stylesheet" href="/leaflet.fullscreen/Control.FullScreen.css">' . PHP_EOL;
            $string .= '<script src="/leaflet.fullscreen/Control.FullScreen.js"></script>' . PHP_EOL;
        }
        $string .= '</head>' . PHP_EOL;
        $string .= '<body>' . PHP_EOL;
        $string .= '<h1><a href="/">Cell Sites Database</a></h1>' . PHP_EOL;
        $string .= $this->generateBody();
        $string .= '</body>' . PHP_EOL;
        $string .= '</html>' . PHP_EOL;
        return($string);
    }
    abstract protected function generateBody(): string;
    protected function setRequiresLeaflet(bool $requiresLeaflet): void
    {
        $this->requiresLeaflet = $requiresLeaflet;
    }
}
