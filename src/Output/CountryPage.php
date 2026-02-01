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
        $string = '<h2>' . $this->country->getName() . '</h2>' . PHP_EOL;
        $string .= '<ul>' . PHP_EOL;
        $string .= sprintf(
            '<li><b>MCC:</b> %s</li>' . PHP_EOL,
            (string)$this->country->getMcc()
        );
        $string .= '</ul>' . PHP_EOL;
        $string .= '<h3>Networks</h3>' . PHP_EOL;
        $string .= '<div class="list-group">' . PHP_EOL;
        foreach ($this->networks as $thisNetwork) {
            $string .= sprintf('<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/network/%s">' . PHP_EOL, (string)$thisNetwork->getUuid());
            $string .= sprintf('<span>%s</span>' . PHP_EOL, $thisNetwork->getName());
            $string .= sprintf('<span class="text-secondary">%03d-%02d</span>' . PHP_EOL, $this->network->getCountry()->getMcc(), $this->network->getMnc());
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
