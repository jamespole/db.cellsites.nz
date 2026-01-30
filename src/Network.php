<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class Network
{
    private const MNC_MAX = 99;
    private const MNC_MIN = 0;
    private const NAME_MAX_LENGTH = 15;
    private Country $country;
    private int $mnc;
    private string $name;
    private readonly UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, Country $country, string $name, int $mnc)
    {
        $this->uuid = $uuid;
        $this->country = $country;
        $this->setName($name);
        $this->setMnc($mnc);
    }
    public function getCountry(): Country
    {
        return($this->country);
    }
    public function getMnc(): int
    {
        return($this->mnc);
    }
    public function getName(): string
    {
        return($this->name);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    private function setMnc(int $mnc): void
    {
        if ($mnc < self::MNC_MIN) {
            throw new RuntimeException(sprintf('Network MNC should not be less than %d.', self::MNC_MIN));
        } elseif ($mnc > self::MNC_MAX) {
            throw new RuntimeException(sprintf('Network MNC should not be more than %d.', self::MNC_MAX));
        }
        $this->mnc = $mnc;
    }
    private function setName(string $name): void
    {
        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new RuntimeException(
                sprintf('Network name should not be longer than %d characters.', self::NAME_MAX_LENGTH)
            );
        }
        $this->name = $name;
    }
}
