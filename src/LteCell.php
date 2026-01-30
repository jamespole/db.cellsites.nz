<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use Ramsey\Uuid\UuidInterface;

class LteCell
{
    private LteChannel $channel;
    private int $cid;
    private LteNode $node;
    private int $pci;
    private ?Site $site;
    private ?UuidInterface $uuid;
    public function __construct(
        ?UuidInterface $uuid,
        LteNode $node,
        int $cid,
        LteChannel $channel,
        int $pci,
        ?Site $site
    ) {
        $this->uuid = $uuid;
        $this->node = $node;
        $this->cid = $cid;
        $this->channel = $channel;
        $this->pci = $pci;
        $this->site = $site;
    }
    public function getChannel(): LteChannel
    {
        return($this->channel);
    }
    public function getCid(): int
    {
        return($this->cid);
    }
    public function getNode(): LteNode
    {
        return($this->node);
    }
    public function getPci(): int
    {
        return($this->pci);
    }
    public function getSite(): ?Site
    {
        return($this->site);
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function getUuid(): ?UuidInterface
    {
        return($this->uuid);
    }
}
