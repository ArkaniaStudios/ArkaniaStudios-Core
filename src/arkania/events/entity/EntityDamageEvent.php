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

final class EntityDamageEvent implements Listener {

    /**
     * @param \pocketmine\event\entity\EntityDamageEvent $event
     * @return void
     */
    public function onEntityDamage(\pocketmine\event\entity\EntityDamageEvent $event): void {
        $player = $event->getEntity();

        if (ProtectionManager::isInProtectedZone($player->getLocation(), 'spawn')) $event->cancel();
    }
}