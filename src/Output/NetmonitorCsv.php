<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\LteCell;
use JamesPole\DbCellsitesNz\Site;
use RuntimeException;

final class NetmonitorCsv extends Output
{
    /** @var LteCell[] */
    private array $lteCells;
    private bool $notFound = false;
    public function __construct(int $mcc, int $mnc)
    {
        parent::__construct('text/plain');
        try {
            $database = new Database();
            $network = $database->getNetworkByPlmn($mcc, $mnc);
            $this->lteCells = $database->getLteCellsByNetwork($network);
        } catch (RuntimeException $e) {
            $this->notFound = true;
        }
    }
    protected function generate(): string
    {
        if ($this->notFound === true) {
            $this->setResponseCode(404);
            $string = 'Error 404: Country/Network Not Found';
            return($string);
        }
        $string = 'tech;mcc;mnc;lac_tac;node_id;cid;psc_pci;band;arfcn;site_name;site_lat;site_long;cell_name;azimuth;height;tilt_mech;tilt_el' . PHP_EOL;
        foreach ($this->lteCells as $thisLteCell) {
            $string .= self::generateLineForLte($thisLteCell);
        }
        return($string);
    }
    private static function generateLineForLte(LteCell $lteCell): string
    {
        $site = self::getSiteForLteCell($lteCell);
        if ($site !== null) {
            $lat = $site->getLocation()->getCoordinate()->getLat();
            $long = $site->getLocation()->getCoordinate()->getLng();
            if ($site->getCode() === null) {
                $name = $site->getName();
            } else {
                $name = sprintf(
                    '%s [%s]',
                    $site->getName(),
                    $site->getCode()
                );
            }
        } else {
            $lat = '';
            $long = '';
            $name = '';
        }
        return(sprintf(
            '3;%d;%d;%d;%d;%d;%d;%d;%d;%s;%f;%f;;;;;' . PHP_EOL,
            $lteCell->getNode()->getArea()->getNetwork()->getCountry()->getMcc(),
            $lteCell->getNode()->getArea()->getNetwork()->getMnc(),
            $lteCell->getNode()->getArea()->getTac(),
            $lteCell->getNode()->getEnb(),
            $lteCell->getCid(),
            $lteCell->getPci(),
            $lteCell->getChannel()->getBand()->getFrequency(),
            $lteCell->getChannel()->getEarfcn(),
            $name,
            $lat,
            $long
        ));
    }
    private static function getSiteForLteCell(LteCell $lteCell): ?Site
    {
        if ($lteCell->getSite() === null) {
            return($lteCell->getNode()->getSite());
        }
        return($lteCell->getSite());
    }
}
