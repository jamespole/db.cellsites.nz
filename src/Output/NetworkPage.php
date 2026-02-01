<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\LteArea;
use JamesPole\DbCellsitesNz\Network;
use JamesPole\DbCellsitesNz\Site;
use Location\Formatter\Coordinate\DecimalDegrees;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class NetworkPage extends Page
{
    /** @var LteArea[] */
    private array $lteAreas;
    private Network $network;
    private bool $notFound = false;
    /** @var Site[] */
    private array $sites;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->network = $database->getNetworkByUuid($uuid);
            $this->lteAreas = $database->getLteAreas($this->network);
            $this->sites = $database->getSitesByNetwork($this->network);
            $this->setRequiresLeaflet(true);
        } catch (RuntimeException $e) {
            $this->setResponseCode(404);
            $this->notFound = true;
        }
    }
    protected function generateBody(): string
    {
        if ($this->notFound === true) {
            return(self::generateNotFound());
        }
        $string = $this->generateBreadcrumbs();
        $string .= $this->generateLteAreaList();
        $string .= '<h3>Map of sites</h3>' . PHP_EOL;
        $string .= '<div id="map" style="height:40em"></div>' . PHP_EOL;
        $string .= '<script>' . PHP_EOL;
        $string .= 'var osm = L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {' . PHP_EOL;
        $string .= ' maxZoom: 19,' . PHP_EOL;
        $string .= ' attribution: \'&copy; <a href="https://osm.org/copyright">OpenStreetMap</a>\'' . PHP_EOL;
        $string .= '});' . PHP_EOL;
        // @codingStandardsIgnoreStart
        $string .= 'var Esri_WorldImagery = L.tileLayer(\'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}\', {' . PHP_EOL;
        $string .= ' attribution: \'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community\'' . PHP_EOL;
        $string .= '});' . PHP_EOL;
        $string .= 'const map = L.map(\'map\', { layers: [osm] });' . PHP_EOL;
        $string .= 'map.addControl(new L.Control.FullScreen());' . PHP_EOL;
        $string .= 'var layerControl = L.control.layers({"OpenStreetMaps": osm, "Esri.WorldImagery": Esri_WorldImagery}).addTo(map);' . PHP_EOL;
        // @codingStandardsIgnoreEnd
        $string .= 'var markers = [' . PHP_EOL;
        foreach ($this->sites as $thisSite) {
            $string .= sprintf(
                ' L.marker([%s]),' . PHP_EOL,
                (string)$thisSite->getLocation()->getCoordinate()->format(new DecimalDegrees(','))
            );
        }
        $string .= '];' . PHP_EOL;
        $string .= 'var group = L.featureGroup(markers).addTo(map);' . PHP_EOL;
        $string .= 'map.fitBounds(group.getBounds());' . PHP_EOL;
        $string .= '</script>' . PHP_EOL;
        $string .= sprintf(
            '<h3>List of sites <small>(%d sites)</small></h3>' . PHP_EOL,
            count($this->sites)
        );
        $string .= '<ul>' . PHP_EOL;
        foreach ($this->sites as $thisSite) {
            $code = $thisSite->getCode();
            if ($code === null) {
                $string .= sprintf(
                    '<li><a href="/site/%s">%s</a></li>' . PHP_EOL,
                    htmlentities((string)$thisSite->getUuid()),
                    htmlentities($thisSite->getName())
                );
            } else {
                $string .= sprintf(
                    '<li><a href="/site/%s">%s</a> (%s)</li>' . PHP_EOL,
                    htmlentities((string)$thisSite->getUuid()),
                    htmlentities($thisSite->getName()),
                    htmlentities($code)
                );
            }
        }
        $string .= '</ul>' . PHP_EOL;
        return($string);
    }
    private function generateBreadcrumbs(): string
    {
        $string = '<nav aria-label="breadcrumb">' . PHP_EOL;
        $string .= '<ol class="breadcrumb mt-3">' . PHP_EOL;
        $string .= '<li class="breadcrumb-item"><a href="/">Home</a></li>' . PHP_EOL;
        $string .= sprintf(
            '<li class="breadcrumb-item"><a href="/country/%s">%s</a></li>' . PHP_EOL,
            htmlentities((string)$this->network->getCountry()->getUuid()),
            htmlentities($this->network->getCountry()->getName())
        );
        $string .= sprintf(
            '<li class="breadcrumb-item active" aria-current="page">%s</li>' . PHP_EOL,
            htmlentities($this->network->getName())
        );
        $string .= '</ol>' . PHP_EOL;
        $string .= '</nav>' . PHP_EOL;
        return($string);
    }
    private function generateLteAreaList(): string
    {
        $string = '<div class="list-group">' . PHP_EOL;
        foreach ($this->lteAreas as $thisLteArea) {
            $string .= sprintf(
                '<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/lte/area/%s">' . PHP_EOL,
                htmlentities((string)$thisLteArea->getUuid())
            );
            $string .= sprintf(
                '<span class="fs-3">%s</span>' . PHP_EOL,
                htmlentities($thisLteArea->getName())
            );
            $string .= sprintf(
                '<span class="text-secondary">%d</span>' . PHP_EOL,
                $thisLteArea->getTac()
            );
            $string .= '</a>' . PHP_EOL;
        }
        $string .= '</div>' . PHP_EOL;
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: Network Not Found</h2>';
        return($string);
    }
}
