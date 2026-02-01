<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Country;
use JamesPole\DbCellsitesNz\Database\Database;

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
    protected function generateContent(): string
    {
        $string = $this->generateCountryList();        
        return($string);
    }
    private function generateCountryList(): string
    {
        $string = '<div class="list-group">' . PHP_EOL;
        foreach ($this->countries as $thisCountry) {
            $string .= sprintf(
                '<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/country/%s">' . PHP_EOL,
                htmlentities((string)$thisCountry->getUuid())
            );
            $string .= sprintf('<span class="fs-1">%s</span>' . PHP_EOL, htmlentities($thisCountry->getName()));
            $string .= sprintf('<span class="text-secondary">%03d</span>' . PHP_EOL, $thisCountry->getMcc());
            $string .= '</a>' . PHP_EOL;
        }
        $string .= '</div>' . PHP_EOL;
        return($string);
    }
}
