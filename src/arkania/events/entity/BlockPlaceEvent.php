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

namespace arkania\events\entity;

use arkania\manager\ProtectionManager;
use pocketmine\event\Listener;

final class BlockPlaceEvent implements Listener {

    /**
     * @param \pocketmine\event\block\BlockPlaceEvent $event
     * @return void
     */
    public function onBlockPlace(\pocketmine\event\block\BlockPlaceEvent $event): void {
        $player = $event->getPlayer();

        if (ProtectionManager::isInProtectedZone($event->getBlock()->getPosition(), 'warzone')) $event->cancel();
    }
}