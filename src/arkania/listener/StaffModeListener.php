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

namespace arkania\listener;

use arkania\Core;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\player\Player;

class StaffModeListener implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();

        if ($this->core->staff->isInStaffMode($player))
            $event->cancel();
    }

    public function onItemTransaction(InventoryTransactionEvent $event){
        $player = $event->getTransaction()->getSource();
        if ($this->core->staff->isInStaffMode($player))
            $event->cancel();
    }

    public function onEntityItemPickup(EntityItemPickupEvent $event){
        $player = $event->getOrigin();

        if ($player instanceof Player)
            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event){
        $player = $event->getPlayer();

        if ($player instanceof Player)

            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }

    public function onEntityDamage(EntityDamageEvent $event){
        $player = $event->getEntity();

        if ($player instanceof Player)
            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }
}