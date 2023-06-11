<?php

namespace arkania\commands\player;

use arkania\tasks\ScoreBoardTask;
use arkania\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\player\Player;

class ScoreBoardCommand extends Command
{
    public function __construct()
    {
        parent::__construct("scoreboard", "Permet d'activer/désactiver le scoreboard", "/scoreboard");
        $this->setPermission("arkania:permission.scoreboard");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        assert($sender instanceof Player);

        if(!isset(ScoreBoardTask::$enabled[$sender->getName()])){
            ScoreBoardTask::$enabled[$sender->getName()] = $sender->getName();
            $sender->sendMessage(Utils::getPrefix() . "§aVous venez d'activer le scoreBoard.");
        }else{
            unset(ScoreBoardTask::$enabled[$sender->getName()]);
            $pk = new RemoveObjectivePacket();
            $pk->objectiveName = $sender->getName();
            $sender->getNetworkSession()->sendDataPacket($pk);
            $sender->sendMessage(Utils::getPrefix() . "§cVous venez de désactiver le scoreBoard.");
        }
    }
}