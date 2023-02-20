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
 * Tous ce qui est développé par nos équipes, ou qui concerne le serveur, restent confidentiels et est interdit à l’utilisation tiers.
 */

namespace arkania\events\entity;

use arkania\manager\FactionManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class EntityDamageEntityEvent implements Listener{

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $player = $event->getDamager();
        $target = $event->getEntity();
        $factionManager = new FactionManager();

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