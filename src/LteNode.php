<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;

class LteNode
{
    private LteArea $area;
    private int $enb;
    private ?Site $site;
    private UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, LteArea $area, int $enb, ?Site $site)
    {
        $this->uuid = $uuid;
        $this->area = $area;
        $this->enb = $enb;
        $this->site = $site;
    }
    public function getArea(): LteArea
    {
        return($this->area);
    }
    public function getEnb(): int
    {
        return($this->enb);
    }
    public function getSite(): ?Site
    {
        return($this->site);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
}
