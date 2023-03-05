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

namespace arkania\events\players;

use arkania\Core;
use pocketmine\event\Listener;

final class PlayerLoginEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param \pocketmine\event\player\PlayerLoginEvent $event
     * @return void
     */
    public function onPlayerLogin(\pocketmine\event\player\PlayerLoginEvent $event): void {
        $player = $event->getPlayer();

        if (!$this->core->getRanksManager()->existPlayer($player->getName()))
            $this->core->getRanksManager()->setDefaultRank($player->getName());

        $this->core->getRanksManager()->register($player);
    }
}