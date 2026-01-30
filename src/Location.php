<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Location\Coordinate;
use Ramsey\Uuid\UuidInterface;

final class Location
{
    private Coordinate $coordinate;
    private ?UuidInterface $uuid;
    public function __construct(?UuidInterface $uuid, Coordinate $coordinate)
    {
        $this->uuid = $uuid;
        $this->coordinate = $coordinate;
    }
    public function getCoordinate(): Coordinate
    {
        return($this->coordinate);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
}
