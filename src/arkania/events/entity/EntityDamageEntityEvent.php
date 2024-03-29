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
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

final class EntityDamageEntityEvent implements Listener{

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $player = $event->getDamager();
        $target = $event->getEntity();
        $factionManager = $this->core->getFactionManager();

        if (ProtectionManager::isInProtectedZone($target->getLocation(), 'spawn')) $event->cancel();

        if ($player instanceof Player && $target instanceof Player) {
            if ($factionManager->getFaction($player->getName()) === $factionManager->getFaction($target->getName()))
                if ($factionManager->getFaction($player->getName()) !== '...')
                    $event->cancel();

            if ($factionManager->getFaction($player->getName()) !== '...'){
                if ($factionManager->getFaction($target->getName()) !== '...'){
                    $allie = $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllies();

                    if (in_array($factionManager->getFaction($target->getName()), $allie))
                        $event->cancel();
                }
            }
        }
    }
}