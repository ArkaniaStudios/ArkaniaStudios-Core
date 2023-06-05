<?php

namespace arkania\tasks;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ScoreBoardTask extends Task
{
    public static array $enabled = [];

    private array $lines = [];

    public function __construct(private Player $player)
    {

    }

    public function onRun(): void
    {
        if ($this->player->isConnected()){
            if(isset(self::$enabled[$this->player->getName()])) {

                $player = $this->player;

                $packet = SetDisplayObjectivePacket::create(SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR, $player->getName(), "§cArkania§fStudios", "dummy", SetDisplayObjectivePacket::SORT_ORDER_ASCENDING);
                $player->getNetworkSession()->sendDataPacket($packet);

                $economy = Core::getInstance()->getEconomyManager()->getMoney($player->getName());
                $rank = Core::getInstance()->getRanksManager()->getPlayerRank($player->getName());
                $faction = Core::getInstance()->getFactionManager()->getFaction($player->getName());

                $linesValue = [
                    0 => " §c§l» Profil:",
                    1 => "   §rPseudo: §e{name}",
                    2 => "   §rGrade: §e{rank}",
                    3 => "   §rArgent: §e{money}",
                    4 => "   §rFaction: §e{faction}",
                    5 => " §c§l» Serveur: §e{server}",
                    6 => "   §rVoteParty: §e{voteparty}§f/§e25",
                    7 => "   §rJoueur: §c §e{players}§f/§e100",
                    8 => "§7arkaniastudios.org",
                ];
                $linesValue = str_replace(["{name}", "{rank}", "{money}", "{faction}", "{server}", "{voteparty}", "{players}"], [$player->getName(), $rank, $economy, $faction, Utils::getServerName(), "0", count(Core::getInstance()->getServer()->getOnlinePlayers())], $linesValue);
                foreach ($linesValue as $id => $text) {
                    $this->addLine($id, $text);
                }
            }
        }else $this->getHandler()->cancel();
    }

    public function addLine(int $id, string $line): void
    {
        $packet = new ScorePacketEntry();
        $packet->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        if(isset($this->lines[$id])){
            $pk = new SetScorePacket();
            $pk->entries[] = $this->lines[$id];
            $pk->type = SetScorePacket::TYPE_REMOVE;
            $this->player->getNetworkSession()->sendDataPacket($pk);
            unset($this->lines[$id]);
        }
        $packet->score = $id;
        $packet->scoreboardId = $id;
        $packet->actorUniqueId = $this->player->getId();
        $packet->objectiveName = $this->player->getName();
        $packet->customName = $line;
        $this->lines[$id] = $packet;
        $pkt = new SetScorePacket();
        $pkt->entries[] = $packet;
        $pkt->type = SetScorePacket::TYPE_CHANGE;
        $this->player->getNetworkSession()->sendDataPacket($pkt);
    }
}