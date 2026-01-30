<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use Ramsey\Uuid\Uuid;

final class Router
{
    private const COUNTRY = '|^/country/(' . self::UUID . ')$|';
    private const LOCATION = '|^/location/(' . self::UUID . ')$|';
    private const LTE_AREA = '|^/lte/area/(' . self::UUID . ')$|';
    private const NETMONITOR = '|^/netmonitor/([0-9]{3})-([0-9]{2})/bts_file\.csv$|';
    private const NETMONSTER = '|^/netmonster/([0-9]{3})-([0-9]{2})\.ntm$|';
    private const NETWORK = '|^/network/(' . self::UUID . ')$|';
    private const SITE = '|^/site/(' . self::UUID . ')$|';
    private const UUID = '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}';
    public static function getOutput(string $uri): Output
    {
        if ($uri === '/') {
            return new HomePage();
        } elseif (preg_match(self::COUNTRY, $uri, $matches) === 1) {
            return new CountryPage(Uuid::fromString($matches[1]));
        } elseif (preg_match(self::LOCATION, $uri, $matches) === 1) {
            return new LocationPage(Uuid::fromString($matches[1]));
        } elseif (preg_match(self::LTE_AREA, $uri, $matches) === 1) {
            return new LteAreaPage(Uuid::fromString($matches[1]));
        } elseif (preg_match(self::NETMONITOR, $uri, $matches) === 1) {
            return new NetmonitorCsv((int)$matches[1], (int)$matches[2]);
        } elseif (preg_match(self::NETMONSTER, $uri, $matches) === 1) {
            return new NetmonsterCsv((int)$matches[1], (int)$matches[2]);
        } elseif (preg_match(self::NETWORK, $uri, $matches) === 1) {
            return new NetworkPage(Uuid::fromString($matches[1]));
        } elseif (preg_match(self::SITE, $uri, $matches) === 1) {
            return new SitePage(Uuid::fromString($matches[1]));
        } else {
            return new ErrorPage(404);
        }
    }
}
