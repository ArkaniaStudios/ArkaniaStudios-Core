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

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class PurifTask extends Task {

    /** @var Core */
    private Core $core;

    /** @var Player */
    private Player $player;

    /** @var int */
    private int $time = 10;

    public function __construct(Core $core, Player $player) {
        $this->core = $core;
        $this->player = $player;
    }

    public function onRun(): void {
        $player = $this->player;

        if ($this->core->getServer()->getPlayerExact($player->getName()) === false)
            $this->getHandler()->cancel();

        if ($this->core->getPurifAnimation()->isInPurifZone($player->getLocation())){
            if ($this->core->getPurifAnimation()->hasItem($player)){
                if ($this->time === 0){
                    $player->getInventory()->removeItem(VanillaItems::DIAMOND());
                    $player->sendMessage(Utils::getPrefix() . "§aPurification terminé !");
                    $this->getHandler()->cancel();
                }else {
                    $player->sendPopup('§aPurification dans §e' . $this->time . '§a.');
                    $this->time--;
                }
            }else
                $this->getHandler()->cancel();
        }else
            $this->getHandler()->cancel();
    }
}
