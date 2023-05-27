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

use arkania\Core;
use arkania\manager\ProtectionManager;
use pocketmine\event\entity\EntityDamageEvent as EntityDamageEventAlias;
use pocketmine\event\Listener;
use pocketmine\player\Player;

final class EntityDamageEvent implements Listener {

    /**
     * @param EntityDamageEventAlias $event
     * @return void
     */
    public function onEntityDamage(EntityDamageEventAlias $event): void {
        $player = $event->getEntity();

        if($player instanceof Player){
            if (ProtectionManager::isInProtectedZone($player->getLocation(), 'spawn')) $event->cancel();
        }

        if ($event->getCause() === EntityDamageEventAlias::CAUSE_VOID){
            Core::getInstance()->getSpawnManager()->teleportSpawn($player);
        }
    }
}