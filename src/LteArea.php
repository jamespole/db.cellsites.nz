<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class LteArea
{
    private const NAME_MAX_LENGTH = 20;
    private const TAC_MAX = 65534;
    private const TAC_MIN = 1;
    private string $name;
    private Network $network;
    private int $tac;
    private UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, Network $network, int $tac, string $name)
    {
        $this->uuid = $uuid;
        $this->network = $network;
        $this->setTac($tac);
        $this->setName($name);
    }
    public function getName(): string
    {
        return($this->name);
    }
    public function getNetwork(): Network
    {
        return($this->network);
    }
    public function getTac(): int
    {
        return($this->tac);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    private function setName(string $name): void
    {
        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new RuntimeException(
                sprintf('LTE area name should not be longer than %d characters.', self::NAME_MAX_LENGTH)
            );
        }
        $this->name = $name;
    }
    private function setTac(int $tac): void
    {
        if ($tac < self::TAC_MIN) {
            throw new RuntimeException(sprintf('LTE area TAC should not be less than %d.', self::TAC_MIN));
        } elseif ($tac > self::TAC_MAX) {
            throw new RuntimeException(sprintf('LTE area TAC sould not be more than %d.', self::TAC_MAX));
        }
        $this->tac = $tac;
    }
}
