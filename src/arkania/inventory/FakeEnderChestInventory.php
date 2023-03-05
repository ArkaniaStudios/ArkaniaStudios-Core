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

namespace arkania\inventory;

use arkania\commands\ranks\EnderChestCommand;
use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class FakeEnderChestInventory extends EnderChestInventory {

    /**
     * @param Player $who
     * @return void
     */
    public function onClose(Player $who): void {
        parent::onClose($who);
        EnderChestCommand::sendFakeBlock([$who], $who->getWorld()->getBlock(new Vector3($who->getPosition()->getX(), $who->getPosition()->getY(), $who->getPosition()->getZ())));
    }
}