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

namespace arkania\factions\claims;

use arkania\manager\FactionManager;
use pocketmine\block\tile\Container;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;

class ClaimListener implements Listener {

    /**
     * @param Player $player
     * @param Position $position
     * @return bool
     */
    public function canAffectArea(Player $player, Position $position): bool {
        $factionManager = new FactionManager();
        $member = $factionManager->getFaction($player->getName());
        $claim = ClaimManager::getInstance()->getClaimByPosition($position);
        if ($claim !== null) return $member === $claim->getFaction();
        return true;
    }

    public function onPlayerInteract(PlayerInteractEvent $event){
        $tile = $event->getBlock()->getPosition()->getWorld()->getTile($event->getBlock()->getPosition());
        if ($tile instanceof Container) {
            if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition()))
                $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event){
        var_dump($this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition()));
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition())) $event->cancel();
    }

    public function onBlockBreak(BlockBreakEvent $event){
        if (!$this->canAffectArea($event->getPlayer(), $event->getBlock()->getPosition())) $event->cancel();
    }
}