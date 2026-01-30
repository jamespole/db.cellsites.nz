<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\LteArea;
use JamesPole\DbCellsitesNz\LteNode;
use Location\Formatter\Coordinate\DecimalDegrees;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class LteAreaPage extends Page
{
    private LteArea $area;
    /** @var LteNode[] */
    private array $nodes;
    private bool $notFound = false;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->area = $database->getLteArea($uuid);
            $this->nodes = $database->getLteNodes($this->area);
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
        $string = '<h2>' . $this->area->getName() . '</h2>' . PHP_EOL;
        $string .= '<ul>' . PHP_EOL;
        $string .= sprintf(
            '<li><b>TAC:</b> %d</li>',
            $this->area->getTac()
        );
        $string .= sprintf(
            '<li><b>Network:</b> <a href="/network/%s">%s</a></li>',
            htmlentities((string)$this->area->getNetwork()->getUuid()),
            htmlentities($this->area->getNetwork()->getName())
        );
        $string .= '</ul>' . PHP_EOL;
        if (count($this->nodes) !== 0) {
            $string .= '<h3>Map of nodes</h3>' . PHP_EOL;
            $string .= '<div id="map" style="height:40em"></div>' . PHP_EOL;
            $string .= '<script>' . PHP_EOL;
            $string .= 'const map = L.map(\'map\', { fullscreenControl:true });' . PHP_EOL;
            $string .= 'var markers = [' . PHP_EOL;
            foreach ($this->nodes as $thisNode) {
                $site = $thisNode->getSite();
                if ($site === null) {
                    continue;
                }
                $string .= sprintf(
                    ' L.marker([%s]),' . PHP_EOL,
                    (string)$site->getLocation()->getCoordinate()->format(new DecimalDegrees(','))
                );
            }
            $string .= '];' . PHP_EOL;
            $string .= 'var group = L.featureGroup(markers).addTo(map);' . PHP_EOL;
            $string .= 'map.fitBounds(group.getBounds());' . PHP_EOL;
            $string .= 'L.tileLayer(\'https://tile.openstreetmap.org/{z}/{x}/{y}.png\', {' . PHP_EOL;
            $string .= ' maxZoom: 19,' . PHP_EOL;
            $string .= ' attribution: \'&copy; <a href="https://osm.org/copyright">OpenStreetMap</a>\'' . PHP_EOL;
            $string .= '}).addTo(map);' . PHP_EOL;
            $string .= '</script>' . PHP_EOL;
            $string .= sprintf(
                '<h3>Nodes <small>(%d nodes)</small></h3>' . PHP_EOL,
                count($this->nodes)
            );
            $string .= '<ul>' . PHP_EOL;
            foreach ($this->nodes as $thisNode) {
                $site = $thisNode->getSite();
                if ($site === null) {
                    $string .= sprintf(
                        '<li>%d</li>',
                        $thisNode->getEnb()
                    );
                } else {
                    if ($site->getCode() !== null) {
                        $string .= sprintf(
                            '<li>%d: <a href="/site/%s">%s</a> (%s)</li>',
                            $thisNode->getEnb(),
                            htmlentities((string)$site->getUuid()),
                            htmlentities($site->getName()),
                            htmlentities($site->getCode())
                        );
                    } else {
                        $string .= sprintf(
                            '<li>%d: <a href="/site/%s">%s</a></li>',
                            $thisNode->getEnb(),
                            htmlentities((string)$site->getUuid()),
                            htmlentities($site->getName())
                        );
                    }
                }
            }
            $string .= '</ul>' . PHP_EOL;
        }
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: LTE Area Not Found</h2>';
        return($string);
    }
}
