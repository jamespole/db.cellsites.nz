<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Output;

use JamesPole\DbCellsitesNz\Database\Database;
use JamesPole\DbCellsitesNz\Site;
use Location\Formatter\Coordinate\DecimalDegrees;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class SitePage extends Page
{
    private bool $notFound = false;
    private Site $site;
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct();
        try {
            $database = new Database();
            $this->site = $database->getSite($uuid);
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
        $string = '<h2>' . $this->site->getName() . '</h2>' . PHP_EOL;
        $string .= '<ul>' . PHP_EOL;
        $string .= sprintf(
            '<li><b>Network:</b> <a href="/network/%s">%s</a></li>',
            htmlentities((string)$this->site->getNetwork()->getUuid()),
            htmlentities($this->site->getNetwork()->getName())
        );
        $string .= sprintf(
            '<li><b>Location:</b> <a href="/location/%s">%s</a></li>',
            htmlentities((string)$this->site->getLocation()->getUuid()),
            htmlentities((string)$this->site->getLocation()->getCoordinate()->format(new DecimalDegrees(',', 3)))
        );
        if ($this->site->getCode() === null) {
            $string .= '<li><b>Code:</b> <i>n/a</i></li>' . PHP_EOL;
        } else {
            $string .= sprintf(
                '<li><b>Code:</b> %s</li>' . PHP_EOL,
                htmlentities((string)$this->site->getCode())
            );
        }
        $string .= '</ul>' . PHP_EOL;
        return($string);
    }
    private static function generateNotFound(): string
    {
        $string = '<h2>Error 404: Site Not Found</h2>';
        return($string);
    }
}
