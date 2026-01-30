<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class LteBand
{
    private int $band;
    private int $frequency;
    private bool $isTdd;
    private UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, int $band, int $frequency, bool $isTdd)
    {
        $this->uuid = $uuid;
        $this->setBand($band);
        $this->setFrequency($frequency);
        $this->isTdd = $isTdd;
    }
    public function getBand(): int
    {
        return($this->band);
    }
    public function getFrequency(): int
    {
        return($this->frequency);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    public function isTdd(): bool
    {
        return($this->isTdd);
    }
    private function setBand(int $band): void
    {
        if ($band <= 0) {
            throw new RuntimeException('LTE band should not be zero or less.');
        }
        $this->band = $band;
    }
    private function setFrequency(int $frequency): void
    {
        if ($frequency <= 0) {
            throw new RuntimeException('LTE band frequency should not be zero or less.');
        }
        $this->frequency = $frequency;
    }
}
