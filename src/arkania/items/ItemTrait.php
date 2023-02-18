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

namespace arkania\items;

use pocketmine\block\Block;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;

trait ItemTrait {

    /**
     * @param Player $player
     * @return bool
     */
    public function onHeld(Player $player): bool {
        return true;
    }

    /**
     * @param Player $player
     * @param Block $block
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onPInteract(Player $player, Block $block, PlayerInteractEvent $event): void {
    }

    /**
     * @param Player $player
     * @param int $value
     * @return bool
     */
    public function onArmorHeld(Player $player, int $value): bool {
        return true;
    }

    /**
     * @param Player $player
     * @param PlayerItemUseEvent $event
     * @return void
     */
    public function onItemUse(Player $player, PlayerItemUseEvent $event): void {

    }

}