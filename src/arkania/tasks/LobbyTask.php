<?php

namespace arkania\tasks;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class LobbyTask extends Task
{
    /** @var Player  */
    private Player $player;

    /** @var Location  */
    private Location $location;

    /** @var int  */
    private int $time = 5;

    public function __construct(Player $player, Location $location)
    {
        $this->player = $player;
        $this->location = $location;
    }

    public function onRun(): void
    {
        if($this->time === 0){
            $this->player->transfer("lobby1");
            $this->getHandler()->cancel();
        }

        if($this->location !== $this->player->getLocation()){
            $this->player->sendPopup("§cTéléportation annulée !");
            $this->getHandler()->cancel();
        }

        $this->player->sendPopup("§cTéléportation dans §e" . $this->time . " §csecondes");
        $this->time++;
    }
}