<?php

namespace arkania\tasks;

use arkania\manager\HomeManager;
use arkania\utils\Utils;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class HomeTask extends Task
{
    /** @var Player  */
    private Player $player;

    /** @var string  */
    private string $homeName;

    /** @var HomeManager  */
    private HomeManager $homeManager;

    /** @var int  */
    private int $x;

    /** @var int  */
    private int $y;

    /** @var int */
    private int $z;

    /** @var int  */
    private int $time = 5;

    public function __construct(Player $player, string $homeName, int $x, int $y, int $z)
    {
        $this->player = $player;
        $this->homeName = $homeName;
        $this->homeManager = new HomeManager($player->getName());
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function onRun(): void
    {
        $playerX = $this->player->getPosition()->getFloorX();
        $playerY = $this->player->getPosition()->getFloorY();
        $playerZ = $this->player->getPosition()->getFloorZ();

        $x = round($this->x);
        $y = round($this->y);
        $z = round($this->z);

        if($playerX != $x or $playerY != $y or $playerZ != $z){
            $this->getHandler()->cancel();
            $this->player->sendPopup("§cTéléportation annulée");
        }

        if($this->time == 0){
            $this->homeManager->teleportHome($this->homeName);
            $this->player->sendMessage(Utils::getPrefix() . "Vous vous êtes bien téléporté au home : §e" . $this->homeName);
            $this->getHandler()->cancel();
        }

        $this->player->sendPopup("§cTéléportation dans §e " . $this->time . " §csecondes");
        $this->time--;
    }
}