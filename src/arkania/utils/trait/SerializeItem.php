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

namespace arkania\utils\trait;

use pocketmine\player\Player;

trait SerializeItem {

    /**
     * @param Player $player
     * @return string
     */
    final public function __serializeInventory(Player $player): string {
        $inventory = $player->getInventory()->getContents();

        $content = [];
        foreach ($inventory as $slot => $item){
            $content[] .= $item.':'.$item->getMeta().':'.$slot;
        }
        return serialize($content);
    }

    final public function __unserializeInventory(string $key, Player $player): void {
        $content = unserialize($key);

        foreach ($content as $item){
            $value = explode(':', $item);
            $player->getInventory()->setItem((int)$value[2], $value[0]);
        }
    }

}