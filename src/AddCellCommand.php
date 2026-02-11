<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz;

use League\CLImate\CLImate;
use JamesPole\DbCellsitesNz\Database\Database;
use PDO;
use PDOException;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class AddCellCommand
{
    private CLImate $cli;
    private Database $database;
    public function __construct()
    {
        $this->cli = new CLImate();
        $this->database = new Database();
    }
    public function main(): void
    {
        $country = $this->askCountry();
        $network = $this->askNetwork($country);
        $lteArea = $this->askLteArea($network);
        $lteNode = $this->askLteNode($lteArea);
        $lteCell = $this->askLteCell($lteNode);
        $this->printCells($lteNode, $lteCell);
        if($this->cli->confirm('Save new LTE cell to database?')->confirmed() === true) {
            $this->database->insertLteCell($lteCell);
            $this->cli->info('Saved to database.');
        }
    }
    private function askCountry(): Country
    {
        while (true) {
            $mcc = (int)$this->cli->input('MCC?')->prompt();
            $country = $this->database->getCountryByMcc($mcc);
            if($country === null) {
                $this->cli->error('Country not found.');
                continue;
            }
            $this->cli->comment($country->getName());
            return($country);
        }
    }
    private function askLteArea(Network $network): LteArea
    {
        while (true) {
            $tac = (int)$this->cli->input('TAC?')->prompt();
            $lteArea = $this->database->getLteAreaByNetworkTac($network, $tac);
            if($lteArea === null) {
                $this->cli->error('LTE Area not found.');
                continue;
            }
            $this->cli->comment($lteArea->getName());
            return($lteArea);
        }
    }
    private function askLteCell(LteNode $node): LteCell
    {
        while (true) {
            $cid = (int)$this->cli->input('CID?')->prompt();
            $lteCell = $this->database->getLteCellByNodeCid($node, $cid);
            if($lteCell !== null) {
                $this->cli->error('LTE Cell already in database.');
                exit();
            }
            $lteCellPci = $this->askLteCellPci();
            $lteCellChannel = $this->askLteCellChannel();
            return(new LteCell(null, $node, $cid, $lteCellChannel, $lteCellPci, null));
        }
    }
    private function askLteCellChannel(): LteChannel
    {
        while (true) {
            $earfcn = (int)$this->cli->input('EARFCN?')->prompt();
            $lteChannel = $this->database->getLteChannelByEarfcn($earfcn);
            if($lteChannel === null) {
                $this->cli->error('LTE Cell Channel not found.');
                continue;
            }
            return($lteChannel);
        }
    }
    private function askLteCellPci(): int
    {
        while (true) {
            $pci = (int)$this->cli->input('PCI?')->prompt();
            if ($pci < 0) {
                $this->cli->error('LTE Cell PCI must be >= 0.');
                continue;
            }
            return($pci);
        }
    }
    private function askLteNode(LteArea $area): LteNode
    {
        while (true) {
            $enb = (int)$this->cli->input('eNb?')->prompt();
            $lteNode = $this->database->getLteNodeByAreaEnb($area, $enb);
            if($lteNode === null) {
                $this->cli->error('LTE Node not found.');
                continue;
            }
            if($lteNode->getSite() === null) {
                $this->cli->comment('(no associated site)');
            } else {
                $this->cli->comment($lteNode->getSite()->getName());
            }
            return($lteNode);
        }
    }
    private function askNetwork(Country $country): Network
    {
        while (true) {
            $input = $this->cli->input('MNC?')->prompt();
            $network = $this->database->getNetworkByPlmn($country->getMcc(), (int)$input);
            if($network === null) {
                $this->cli->error('Network not found.');
                continue;
            }
            $this->cli->comment($network->getName());
            return($network);
        }
    }
    private function printCells(LteNode $node, LteCell $newCell): void
    {
        $cells = $this->database->getLteCellsByNode($node);
        array_push($cells, $newCell);
        usort($cells, function(LteCell $a, LteCell $b) {
            return($a->getCid() <=> $b->getCid());
        });
        $this->cli->out('CID Band EARFCN PCI');
        $this->cli->out('--- ---- ------ ---');
        foreach ($cells as $thisCell) {
            $string = sprintf(
                '% 3d % 4d % 6d % 3d',
                $thisCell->getCid(),
                $thisCell->getChannel()->getBand()->getBand(),
                $thisCell->getChannel()->getEarfcn(),
                $thisCell->getPci()
            );
            if($thisCell === $newCell) {
                $this->cli->comment($string);
            } else {
                $this->cli->whisper($string);
            }
        }
    }
}
