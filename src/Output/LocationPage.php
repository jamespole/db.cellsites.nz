<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\Location;
use JamesPole\DbCellsitesNz\Site;
use Location\Formatter\Coordinate\DecimalDegrees;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class LocationPage extends Page
{
    private Location $location;
    private bool $notFound = false;
    /** @var Site[] */
    private array $sites;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->location = $database->getLocation($uuid);
            $this->sites = $database->getSitesByLocation($this->location);
            $this->setRequiresLeaflet(true);
        } catch (RuntimeException $e) {
            $this->setResponseCode(404);
            $this->notFound = true;
        }
    }
    protected function generateContent(): string
    {
        if ($this->notFound === true) {
            return(self::generateNotFound());
        }
        $string = $this->generateBreadcrumbs();
        if (count($this->sites) !== 0) {
            $string .= '<h3>Sites</h3>' . PHP_EOL;
            $string .= '<ul>' . PHP_EOL;
            foreach ($this->sites as $thisSite) {
                $string .= sprintf(
                    '<li>%s: <a href="/site/%s">%s</a></li>',
                    htmlentities($thisSite->getNetwork()->getName()),
                    htmlentities((string)$thisSite->getUuid()),
                    htmlentities($thisSite->getName())
                );
            }
            $string .= '</ul>' . PHP_EOL;
        }
        $string .= $this->generateMap();
        return($string);
    }
    private function generateBreadcrumbs(): string
    {
        $string = '<nav aria-label="breadcrumb">' . PHP_EOL;
        $string .= '<ol class="breadcrumb m-3">' . PHP_EOL;
        $string .= '<li class="breadcrumb-item"><a href="/">Home</a></li>' . PHP_EOL;
        $string .= sprintf(
            '<li class="breadcrumb-item active" aria-current="page">%s</li>' . PHP_EOL,
            htmlentities((string)$this->location->getCoordinate()->format(new DecimalDegrees(',', 4)))
        );
        $string .= '</ol>' . PHP_EOL;
        $string .= '</nav>' . PHP_EOL;
        return($string);
    }
    private function generateMap(): string
    {
        $string = '<div id="map" style="height:30em"></div>' . PHP_EOL;
        $string .= '<script>' . PHP_EOL;
        $string .= sprintf(
            'const map = L.map(\'map\').setView([%s], 15);' . PHP_EOL,
            (string)$this->location->getCoordinate()->format(new DecimalDegrees(','))
        );
        $string .= 'map.addControl(new L.Control.FullScreen());' . PHP_EOL;
        $string .= sprintf(
            'L.marker([%s]).addTo(map);' . PHP_EOL,
            (string)$this->location->getCoordinate()->format(new DecimalDegrees(','))
        );
        $string .= 'L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {' . PHP_EOL;
        $string .= ' maxZoom: 19,' . PHP_EOL;
        $string .= ' attribution: \'&copy; <a href="https://osm.org/copyright">OpenStreetMap</a>\'' . PHP_EOL;
        $string .= '}).addTo(map);' . PHP_EOL;
        $string .= '</script>' . PHP_EOL;
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: Location Not Found</h2>' ;
        return($string);
    }
}
