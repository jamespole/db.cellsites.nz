<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Country;
use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\Network;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class CountryPage extends Page
{
    private Country $country;
    /** @var Network[] */
    private array $networks;
    private bool $notFound = false;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->country = $database->getCountryByUuid($uuid);
            $this->networks = $database->getNetworks($this->country);
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
        $string = '<nav aria-label="breadcrumb">' . PHP_EOL;
        $string .= '<ol class="breadcrumb mt-3">' . PHP_EOL;
        $string .= '<li class="breadcrumb-item"><a href="/">Home</a></li>' . PHP_EOL;
        $string .= sprintf(
            '<li class="breadcrumb-item active" aria-current="page">%s</li>' . PHP_EOL,
            htmlentities($this->country->getName())
        );
        $string .= '</ol>' . PHP_EOL;
        $string .= '</nav>' . PHP_EOL;
        $string .= $this->generateNetworkList();
        return($string);
    }
    private function generateNetworkList(): string
    {
        $string = '<div class="list-group">' . PHP_EOL;
        foreach ($this->networks as $thisNetwork) {
            $string .= sprintf(
                '<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/network/%s">' . PHP_EOL,
                htmlentities((string)$thisNetwork->getUuid())
            );
            $string .= sprintf(
                '<span class="fs-1">%s</span>' . PHP_EOL,
                htmlentities($thisNetwork->getName())
            );
            $string .= sprintf(
                '<span class="text-secondary">%03d-%02d</span>' . PHP_EOL,
                $thisNetwork->getCountry()->getMcc(),
                $thisNetwork->getMnc()
            );
            $string .= '</a>' . PHP_EOL;
        }
        $string .= '</div>' . PHP_EOL;
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: Country Not Found</h2>';
        return($string);
    }
}
