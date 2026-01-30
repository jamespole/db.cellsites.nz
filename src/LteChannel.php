<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class LteChannel
{
    private LteBand $band;
    private int $earfcn;
    private UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, LteBand $band, int $earfcn)
    {
        $this->uuid = $uuid;
        $this->band = $band;
        $this->setEarfcn($earfcn);
    }
    public function getBand(): LteBand
    {
        return($this->band);
    }
    public function getEarfcn(): int
    {
        return($this->earfcn);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    private function setEarfcn(int $earfcn): void
    {
        if ($earfcn < 0) {
            throw new RuntimeException('LTE channel EARFCN should not be less than zero.');
        }
        $this->earfcn = $earfcn;
    }
}
