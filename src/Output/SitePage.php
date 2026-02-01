<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\Site;
use Location\Formatter\Coordinate\DecimalDegrees;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class SitePage extends Page
{
    private bool $notFound = false;
    private Site $site;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->site = $database->getSite($uuid);
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
        $string .= $this->generateMap();
        $string .= '<ul>' . PHP_EOL;
        if ($this->site->getCode() === null) {
            $string .= '<li><b>Code:</b> <i>n/a</i></li>' . PHP_EOL;
        } else {
            $string .= sprintf(
                '<li><b>Code:</b> %s</li>' . PHP_EOL,
                htmlentities((string)$this->site->getCode())
            );
        }
        $string .= '</ul>' . PHP_EOL;
        $string .= sprintf(
            '<p><a class="btn btn-info" href="/location/%s" role="button">Location</a></p>' . PHP_EOL,
            htmlentities((string)$this->site->getLocation()->getUuid())
        );
        return($string);
    }
    private function generateBreadcrumbs(): string
    {
        $string = '<nav aria-label="breadcrumb">' . PHP_EOL;
        $string .= '<ol class="breadcrumb m-3">' . PHP_EOL;
        $string .= '<li class="breadcrumb-item"><a href="/">Home</a></li>' . PHP_EOL;
        $string .= sprintf(
            '<li class="breadcrumb-item"><a href="/country/%s">%s</a></li>' . PHP_EOL,
            htmlentities((string)$this->site->getNetwork()->getCountry()->getUuid()),
            htmlentities($this->site->getNetwork()->getCountry()->getName())
        );
        $string .= sprintf(
            '<li class="breadcrumb-item"><a href="/network/%s">%s</a></li>' . PHP_EOL,
            htmlentities((string)$this->site->getNetwork()->getUuid()),
            htmlentities($this->site->getNetwork()->getName())
        );
        $string .= sprintf(
            '<li class="breadcrumb-item active" aria-current="page">%s</li>' . PHP_EOL,
            htmlentities($this->site->getName())
        );
        $string .= '</ol>' . PHP_EOL;
        $string .= '</nav>' . PHP_EOL;
        return($string);
    }
    private function generateMap(): string
    {
        $string = '<div id="map" style="height:30em"></div>' . PHP_EOL;
        $string .= '<script>' . PHP_EOL;
        $string .= 'var osm = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {' . PHP_EOL;
        $string .= ' maxZoom: 19,' . PHP_EOL;
        $string .= ' attribution: \'&copy; <a href="https://osm.org/copyright">OpenStreetMap</a>\'' . PHP_EOL;
        $string .= '});' . PHP_EOL;
        $string .= 'var Esri_WorldImagery = L.tileLayer(\'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}\', {' . PHP_EOL;
        $string .= ' attribution: \'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community\'' . PHP_EOL;
        $string .= '});' . PHP_EOL;
        $string .= sprintf(
            'const map = L.map(\'map\', { layers: [osm] }).setView([%s], 15);' . PHP_EOL,
            (string)$this->site->getLocation()->getCoordinate()->format(new DecimalDegrees(','))
        );
        $string .= 'map.addControl(new L.Control.FullScreen());' . PHP_EOL;
        $string .= 'var layerControl = L.control.layers({"OpenStreetMaps": osm, "Esri.WorldImagery": Esri_WorldImagery}).addTo(map);' . PHP_EOL;
        $string .= sprintf(
            'L.marker([%s]).addTo(map);' . PHP_EOL,
            (string)$this->site->getLocation()->getCoordinate()->format(new DecimalDegrees(','))
        );
        $string .= '</script>' . PHP_EOL;
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: Site Not Found</h2>';
        return($string);
    }
}
