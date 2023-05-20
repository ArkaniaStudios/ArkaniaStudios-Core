<?php

declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 *
 */

namespace arkania\tasks;

use arkania\commands\player\SpawnCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class TeleportTask extends Task {

    /** @var Core */
    private Core $core;

    /** @var Player */
    private Player $player;

    /** @var string */
    private string $type;

    /** @var float */
    private float $x;

    /** @var float */
    private float $y;

    /** @var float */
    private float $z;

    private int $time = 5;

    public function __construct(Core $core,Player $player, string $type, float $x, float $y, float $z) {
        $this->core = $core;
        $this->player = $player;
        $this->type = $type;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function onRun(): void {

        $player = $this->player;

        if ($this->core->getServer()->getPlayerExact($player->getName()) === false){
            unset(SpawnCommand::$teleport[$player->getName()]);
            $this->getHandler()->cancel();
        }

        $playerX = round($player->getPosition()->getX());
        $playerY = round($player->getPosition()->getY());
        $playerZ = round($player->getPosition()->getZ());
        $x = round($this->x);
        $y = round($this->y);
        $z = round($this->z);

        if (($playerX != $x) || ($playerY != $y) || ($playerZ != $z)){
            $player->sendTip("§cTéléportation annulé");
            unset(SpawnCommand::$teleport[$player->getName()]);
            $this->getHandler()->cancel();
        }

        if ($this->time == 0) {
            if ($this->type === 'spawn'){
                unset(SpawnCommand::$teleport[$player->getName()]);
                $this->core->getSpawnManager()->teleportSpawn($player);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté au spawn.");
            }elseif($this->type === 'box') {
                unset(SpawnCommand::$teleport[$player->getName()]);
                $this->core->getBoxManager()->teleportBox($player);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté aux box.");
            }else
                return;
            $this->getHandler()->cancel();
        }else
            $player->sendPopup('§cTéléportation dans §e' . $this->time . '§c.');
        $this->time--;
    }

}