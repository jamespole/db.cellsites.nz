<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Database;

use JamesPole\DbCellsitesNz\Country;
use JamesPole\DbCellsitesNz\Location;
use JamesPole\DbCellsitesNz\LteArea;
use JamesPole\DbCellsitesNz\LteBand;
use JamesPole\DbCellsitesNz\LteCell;
use JamesPole\DbCellsitesNz\LteChannel;
use JamesPole\DbCellsitesNz\LteNode;
use JamesPole\DbCellsitesNz\Network;
use JamesPole\DbCellsitesNz\Site;
use Location\Coordinate;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Database
{
    private PDOWrapper $pdo;
    public function __construct()
    {
        $this->pdo = new PDOWrapper();
    }
    /** @param array<array-key,mixed> $row */
    private static function createCountry(array $row): Country
    {
        assert(is_string($row['id']));
        return(new Country(Uuid::fromString($row['id']), (string)$row['name'], (int)$row['mcc']));
    }
    /** @param array<array-key,mixed> $row */
    private static function createLocation(array $row): Location
    {
        assert(is_string($row['id']));
        return(new Location(
            Uuid::fromString($row['id']),
            new Coordinate((float)$row['lat'], (float)$row['long'])
        ));
    }
    /** @param array<array-key,mixed> $row */
    private static function createLteArea(array $row, Network $network): LteArea
    {
        assert(is_string($row['id']));
        return(new LteArea(Uuid::fromString($row['id']), $network, (int)$row['tac'], (string)$row['name']));
    }
    /** @param array<array-key,mixed> $row */
    private static function createLteBand(array $row): LteBand
    {
        assert(is_string($row['id']));
        assert(is_int($row['band']));
        assert(is_int($row['freq']));
        return(new LteBand(Uuid::fromString($row['id']), $row['band'], $row['freq'], (bool)$row['tdd']));
    }
    /** @param array<array-key,mixed> $row */
    private static function createLteCell(array $row, LteNode $node, LteChannel $channel, ?Site $site = null): LteCell
    {
        assert(is_string($row['id']));
        assert(is_int($row['cid']));
        assert(is_int($row['pci']));
        return(new LteCell(Uuid::fromString($row['id']), $node, $row['cid'], $channel, $row['pci'], $site));
    }
    /** @param array<array-key, mixed> $row */
    private static function createLteChannel(array $row, LteBand $band): LteChannel
    {
        assert(is_string($row['id']));
        assert(is_int($row['earfcn']));
        return(new LteChannel(Uuid::fromString($row['id']), $band, $row['earfcn']));
    }
    /** @param array<array-key,mixed> $row */
    private static function createLteNode(array $row, LteArea $area, ?Site $site = null): LteNode
    {
        assert(is_string($row['id']));
        return(new LteNode(Uuid::fromString($row['id']), $area, (int)$row['enb'], $site));
    }
    /** @param array<array-key,mixed> $row */
    private static function createNetwork(array $row, Country $country): Network
    {
        assert(is_string($row['id']));
        return(new Network(Uuid::fromString($row['id']), $country, (string)$row['name'], (int)$row['mnc']));
    }
    /** @param array<array-key,mixed> $row */
    private static function createSite(array $row, Network $network, Location $location): Site
    {
        assert(is_string($row['id']));
        if ($row['code'] === null) {
            $code = null;
        } else {
            $code = (string)$row['code'];
        }
        return(new Site(Uuid::fromString($row['id']), $network, $location, (string)$row['name'], $code));
    }
    public function getCountryByMcc(int $mcc): ?Country
    {
        $row = $this->pdo->getRow('SELECT id, name, mcc FROM country WHERE mcc = :mcc', array((string)$mcc));
        if($row === null) {
            return(null);
        }
        return(self::createCountry($row));
    }
    public function getCountryByUuid(UuidInterface $uuid): Country
    {
        $row = $this->pdo->getRow('SELECT id, name, mcc FROM country WHERE id = :id', array((string)$uuid));
        return(self::createCountry($row));
    }
    /** @return Country[] */
    public function getCountries(): array
    {
        /** @var Country[] */
        $countries = [];
        foreach ($this->pdo->getRows('SELECT id, name, mcc FROM country ORDER BY name') as $thisRow) {
            $countries[] = self::createCountry($thisRow);
        }
        return($countries);
    }
    public function getLocation(UuidInterface $uuid): Location
    {
        $row = $this->pdo->getRow(
            'SELECT id, lat, long FROM location WHERE id = :id',
            array((string)$uuid)
        );
        return(self::createLocation($row));
    }
    /** @return Location[] */
    public function getLocations(): array
    {
        /** @var Location[] */
        $locations = [];
        $rows = $this->pdo->getRows('SELECT id, lat, long FROM location ORDER BY lat, long');
        foreach ($rows as $thisRow) {
            $locations[] = self::createLocation($thisRow);
        }
        return($locations);
    }
    public function getLteArea(UuidInterface $uuid): LteArea
    {
        $row = $this->pdo->getRow(
            'SELECT id, network, tac, name FROM lte_area WHERE id = :id',
            array((string)$uuid)
        );
        assert(is_string($row['network']));
        return(self::createLteArea($row, $this->getNetworkByUuid(Uuid::fromString($row['network']))));
    }
    public function getLteAreaByNetworkTac(Network $network, int $tac): ?LteArea
    {
            $row = $this->pdo->getRow(
                'SELECT id, network, tac, name FROM lte_area WHERE network = :network AND tac = :tac',
                array($network->getUuid(), $tac)
            );
            if($row === null) {
                return(null);
            }
            return(self::createLteArea($row, $network));
    }
    /** @return LteArea[] */
    public function getLteAreas(Network $network): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, tac, name FROM lte_area WHERE network = :network ORDER BY tac',
            array((string)$network->getUuid())
        );
        /** @var LteArea[] */
        $lteAreas = [];
        foreach ($statement as $thisRow) {
            $lteAreas[] = self::createLteArea($thisRow, $network);
        }
        return($lteAreas);
    }
    public function getLteBand(UuidInterface $uuid): LteBand
    {
        $row = $this->pdo->getRow(
            'SELECT id, band, freq, tdd FROM lte_band WHERE id = :id',
            array((string)$uuid)
        );
        return(self::createLteBand($row));
    }
    /** @return LteCell[] */
    public function getLteCellsByNetwork(Network $network): array
    {
        $areas = $this->getLteAreas($network);
        $cells = [];
        foreach ($areas as $thisArea) {
            $nodes = $this->getLteNodes($thisArea);
            foreach ($nodes as $thisNode) {
                $nodeCells = $this->getLteCellsByNode($thisNode);
                // TODO see if there is a function to simply add results from the function call to $cells
                foreach ($nodeCells as $thisNodeCell) {
                    $cells[] = $thisNodeCell;
                }
            }
        }
        return($cells);
    }
    /** @return LteCell[] */
    public function getLteCellsByNode(LteNode $node): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, cid, earfcn, pci, site FROM lte_cell WHERE node = :node ORDER BY cid',
            array((string)$node->getUuid())
        );
        /** @var LteCell[] */
        $cells = [];
        foreach ($statement as $thisRow) {
            assert(is_string($thisRow['earfcn']));
            if ($thisRow['site'] === null) {
                $cells[] = self::createLteCell(
                    $thisRow,
                    $node,
                    $this->getLteChannel(Uuid::fromString($thisRow['earfcn']))
                );
            } else {
                $cells[] = self::createLteCell(
                    $thisRow,
                    $node,
                    $this->getLteChannel(Uuid::fromString($thisRow['earfcn'])),
                    $this->getSite(Uuid::fromString($thisRow['site']))
                );
            }
        }
        return($cells);
    }
    public function getLteCellByNodeCid(LteNode $node, int $cid): ?LteCell
    {
        $row = $this->pdo->getRow(
            'SELECT id, cid, earfcn, pci, site FROM lte_cell WHERE node = :node AND cid = :cid',
            array($node->getUuid(), $cid)
        );
        if ($row === null) {
            return(null);
        }
        if($row['site'] === null) {
            return(self::createLteCell(
                $row,
                $node,
                $this->getLteChannel(Uuid::fromString($row['earfcn']))
            ));
        } else {
            return(self::createLteCell(
                $row,
                $node,
                $this->getLteChannel(Uuid::fromString($row['earfcn'])),
                $this->getSite(Uuid::fromString($row['site']))
            ));
        }
    }
    public function getLteChannel(UuidInterface $uuid): LteChannel
    {
        $row = $this->pdo->getRow(
            'SELECT id, earfcn, band FROM lte_earfcn WHERE id = :id',
            array((string)$uuid)
        );
        assert(is_string($row['band']));
        return(self::createLteChannel($row, $this->getLteBand(Uuid::fromString($row['band']))));
    }
    public function getLteChannelByEarfcn(int $earfcn): ?LteChannel
    {
        $row = $this->pdo->getRow(
            'SELECT id, earfcn, band FROM lte_earfcn WHERE earfcn = :earfcn',
            array($earfcn)
        );
        if ($row === null) {
            return(null);
        }
        return(self::createLteChannel($row, $this->getLteBand(Uuid::fromString($row['band']))));
    }
    public function getLteNodeByAreaEnb(LteArea $area, int $enb): ?LteNode
    {
        $row = $this->pdo->getRow(
            'SELECT id, enb, site FROM lte_node WHERE area = :area AND enb = :enb',
            array($area->getUuid(), $enb)
        );
        if($row === null) {
            return(null);
        }
        if ($row['site'] === null) {
            return(self::createLteNode($row, $area));
        }
        return(self::createLteNode($row, $area, $this->getSite(uuid::fromString($row['site']))));
    }
    /** @return LteNode[] */
    public function getLteNodes(LteArea $area): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, enb, site FROM lte_node WHERE area = :area ORDER BY enb',
            array((string)$area->getUuid())
        );
        /** @var LteNode[] */
        $lteNodes = [];
        foreach ($statement as $thisRow) {
            if ($thisRow['site'] === null) {
                $lteNodes[] = self::createLteNode($thisRow, $area);
            } else {
                $lteNodes[] = self::createLteNode($thisRow, $area, $this->getSite(uuid::fromString($thisRow['site'])));
            }
        }
        return($lteNodes);
    }
    public function getNetworkByPlmn(int $mcc, int $mnc): ?Network
    {
        $country = $this->getCountryByMcc($mcc);
        if($country === null) {
            return(null);
        }
        $row = $this->pdo->getRow(
            'SELECT id, country, name, mnc FROM network WHERE country = :country AND mnc = :mnc',
            array((string)$country->getUuid(), (string)$mnc)
        );
        if($row === null) {
            return(null);
        }
        return(self::createNetwork($row, $country));
    }
    public function getNetworkByUuid(UuidInterface $uuid): Network
    {
        $row = $this->pdo->getRow(
            'SELECT id, country, name, mnc FROM network WHERE id = :id',
            array((string)$uuid)
        );
        assert(is_string($row['country']));
        return(self::createNetwork($row, $this->getCountryByUuid(Uuid::fromString($row['country']))));
    }
    /** @return Network[] */
    public function getNetworks(Country $country): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, name, mnc FROM network WHERE country = :country',
            array((string)$country->getUuid())
        );
        /** @var Network[] */
        $networks = [];
        foreach ($statement as $thisRow) {
            $networks[] = self::createNetwork($thisRow, $country);
        }
        return($networks);
    }
    public function getSite(UuidInterface $uuid): Site
    {
        $row = $this->pdo->getRow(
            'SELECT id, network, location, name, code FROM site WHERE id = :id',
            array((string)$uuid)
        );
        assert(is_string($row['network']));
        assert(is_string($row['location']));
        return(self::createSite(
            $row,
            $this->getNetworkByUuid(Uuid::fromString($row['network'])),
            $this->getLocation(Uuid::fromString($row['location']))
        ));
    }
    /** @return Site[] */
    public function getSitesByLocation(Location $location): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, network, name, code FROM site WHERE location = :location ORDER BY name',
            array((string)$location->getUuid())
        );
        /** @var Site[] */
        $sites = [];
        foreach ($statement as $thisRow) {
            assert(!is_null($thisRow['network']));
            $network = $this->getNetworkByUuid(Uuid::fromString($thisRow['network']));
            $sites[] = $this->createSite($thisRow, $network, $location);
        }
        return($sites);
    }
    /** @return Site[] */
    public function getSitesByNetwork(Network $network): array
    {
        $statement = $this->pdo->getRows(
            'SELECT id, location, name, code FROM site WHERE network = :network ORDER BY name',
            array((string)$network->getUuid())
        );
        /** @var Site[] */
        $sites = [];
        foreach ($statement as $thisRow) {
            assert(!is_null($thisRow['location']));
            $location = $this->getLocation(Uuid::fromString($thisRow['location']));
            $sites[] = $this->createSite($thisRow, $network, $location);
        }
        return($sites);
    }
    public function insertLteCell(LteCell $lteCell): void
    {
        $this->pdo->query(
            'INSERT INTO lte_cell (node, cid, earfcn, pci) VALUES (:node, :cid, :earfcn, :pci)',
            array(
                $lteCell->getNode()->getUuid(),
                $lteCell->getCid(),
                $lteCell->getChannel()->getUuid(),
                $lteCell->getPci()
            )
        );
    }
}
