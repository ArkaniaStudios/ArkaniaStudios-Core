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
use arkania\manager\RanksManager;
use pocketmine\event\Listener;

final class PlayerQuitEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerQuit(\pocketmine\event\player\PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        /* Nick */
        if ($this->core->getNickManager()->isNick($player))
            $this->core->getNickManager()->removePlayerNick($player);

        /* Ranks */
        $this->core->getRanksManager()->unRegister($player);

        /* Connection */
        $this->core->getStatsManager()->removeServerConnection($player);

        /* StaffMode */
        if ($this->core->getStaffManager()->isInStaffMode($player))
            $this->core->getStaffManager()->removeStaffMode($player);

        /* QuitMessage */
        $event->setQuitMessage('[§c-§f] ' . RanksManager::getRanksFormatPlayer($player));

    }
}