<?php
declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _             __     __  ____
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \            \ \   / / |___ \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \    _____   \ \ / /    __) |
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \  |_____|   \ V /    / __/
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\            \_/    |_____|
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 */

namespace arkania\events\entity;

use arkania\manager\ProtectionManager;
use pocketmine\event\Listener;

class BlockBurnEvent implements Listener {

    public function onBlockBurn(\pocketmine\event\block\BlockBurnEvent $event): void
    {
        $block = $event->getBlock();
        if (ProtectionManager::isInProtectedZone($block->getPosition(), 'warzone')) {
            $event->cancel();
        }
    }

}