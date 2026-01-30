<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Country;
use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\Location;
use Location\Formatter\Coordinate\DecimalDegrees;

final class HomePage extends Page
{
    /** @var Country[] */
    private array $countries = [];
    public function __construct()
    {
        parent::__construct();
        $database = new Database();
        $this->countries = $database->getCountries();
    }
    protected function generateBody(): string
    {
        $string = '<h2>Countries</h2>' . PHP_EOL;
        $string .= '<ul>' . PHP_EOL;
        foreach ($this->countries as $thisCountry) {
            $string .= '<li><a href="/country/' .
                htmlentities((string)$thisCountry->getUuid()) . '">' .
                htmlentities($thisCountry->getName()) . '</a></li>' . PHP_EOL;
        }
        $string .= '</ul>' . PHP_EOL;
        return($string);
    }
}
