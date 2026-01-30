<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class Site
{
    public const CODE_MAX_LENGTH = 15;
    public const NAME_MAX_LENGTH = 50;
    private ?string $code;
    private Location $location;
    private string $name;
    private Network $network;
    private readonly UuidInterface $uuid;
    public function __construct(UuidInterface $uuid, Network $network, Location $location, string $name, ?string $code)
    {
        $this->uuid = $uuid;
        $this->network = $network;
        $this->location = $location;
        $this->setName($name);
        $this->setCode($code);
    }
    public function getCode(): ?string
    {
        return($this->code);
    }
    public function getLocation(): Location
    {
        return($this->location);
    }
    public function getName(): string
    {
        return($this->name);
    }
    public function getNetwork(): Network
    {
        return($this->network);
    }
    public function getUuid(): UuidInterface
    {
        return($this->uuid);
    }
    private function setCode(?string $code): void
    {
        if ($code !== null && mb_strlen($code) > self::CODE_MAX_LENGTH) {
            throw new RuntimeException(
                sprintf('Side code should not be longer than %d characters.', self::CODE_MAX_LENGTH)
            );
        }
        $this->code = $code;
    }
    private function setName(string $name): void
    {
        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new RuntimeException(
                sprintf('Site name should not be longer than %d characters.', self::NAME_MAX_LENGTH)
            );
        }
        $this->name = $name;
    }
}
