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
        $string = '<!doctype html>' . PHP_EOL;
        $string .= '<html lang="en">' . PHP_EOL;
        $string .= '<head>' . PHP_EOL;
        $string .= '<meta charset="utf-8">' . PHP_EOL;
        $string .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $string .= '<title>Cell Sites Database</title>' . PHP_EOL;
        $string .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">' . PHP_EOL;
        if ($this->requiresLeaflet === true) {
            $string .= '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>' . PHP_EOL;
            $string .= '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>' . PHP_EOL;
            $string .= '<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@5.3.0/dist/Control.FullScreen.css" />' . PHP_EOL;
            $string .= '<script src="https://unpkg.com/leaflet.fullscreen@5.3.0/dist/Control.FullScreen.umd.js"></script>' . PHP_EOL;
        }
        $string .= '</head>' . PHP_EOL;
        $string .= '<body>' . PHP_EOL;
        $string .= '<div class="container">' . PHP_EOL;
        $string .= '<nav class="navbar bg-body-tertiary">' . PHP_EOL;
        $string .= '<div class="container-fluid">' . PHP_EOL;
        $string .= '<a class="navbar-brand" href="/">Cell Sites Database</a>' . PHP_EOL;
        $string .= '</div>' . PHP_EOL;
        $string .= '</nav>' . PHP_EOL;
        $string .= $this->generateBody();
        $string .= '</div>' . PHP_EOL;
        $string .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>' . PHP_EOL;
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
