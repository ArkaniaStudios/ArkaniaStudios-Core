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

final class BlockBreakEvent implements Listener {

    /**
     * @param \pocketmine\event\block\BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreak(\pocketmine\event\block\BlockBreakEvent $event): void {
        $player = $event->getPlayer();

        if (ProtectionManager::isInProtectedZone($event->getBlock()->getPosition(), 'warzone')) {
            if (!$player->getServer()->isOp($player->getName())){
                $event->cancel();
            }
        }
    }
}