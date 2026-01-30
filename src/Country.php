<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class Country
{
    private const MCC_MAX = 999;
    private const MCC_MIN = 100;
    private const NAME_MAX_LENGTH = 15;
    private int $mcc;
    private string $name;
    private readonly UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, string $name, int $mcc)
    {
        $this->uuid = $uuid;
        $this->setName($name);
        $this->setMcc($mcc);
    }
    public function getMcc(): int
    {
        return($this->mcc);
    }
    public function getName(): string
    {
        return($this->name);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    private function setMcc(int $mcc): void
    {
        if ($mcc < self::MCC_MIN) {
            throw new RuntimeException(sprintf('Country MCC should not be less than %d.', self::MCC_MIN));
        } elseif ($mcc > self::MCC_MAX) {
            throw new RuntimeException(sprintf('Country MCC should not be more than %d.', self::MCC_MAX));
        }
        $this->mcc = $mcc;
    }
    private function setName(string $name): void
    {
        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new RuntimeException(
                sprintf('Country name should not be longer than %d characters.', self::NAME_MAX_LENGTH)
            );
        }
        $this->name = $name;
    }
}
