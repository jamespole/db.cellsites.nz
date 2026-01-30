<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\LteCell;
use JamesPole\DbCellsitesNz\Site;
use RuntimeException;

final class NetmonsterCsv extends Output
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
        $string = '';
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
            '4G;%d;%d;%d;%d;%d;%d;%f;%f;%s;%d' . PHP_EOL,
            $lteCell->getNode()->getArea()->getNetwork()->getCountry()->getMcc(),
            $lteCell->getNode()->getArea()->getNetwork()->getMnc(),
            $lteCell->getCid(),
            $lteCell->getNode()->getArea()->getTac(),
            $lteCell->getNode()->getEnb(),
            $lteCell->getPci(),
            $lat,
            $long,
            $name,
            $lteCell->getChannel()->getEarfcn()
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
