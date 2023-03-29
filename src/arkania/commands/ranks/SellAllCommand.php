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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class SellAllCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('sellall',
        'SellAll - ArkaniaStudios',
        '/sellall');
        $this->setPermission('arkania:permission.sellhand');
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        if ($this->contain($player, VanillaBlocks::DIRT()->asItem())){
            $count = $this->countItem($player, VanillaBlocks::DIRT()->asItem()->getId());
            $player->getInventory()->removeItem(VanillaBlocks::DIRT()->asItem());
        }
        return true;
    }

    private function contain(Player $player, Item $item): bool {
        return $player->getInventory()->contains($item);
    }

    /**
     * @param Player $player
     * @param int $id
     * @return int
     */
    private function countItem(Player $player, int $id): int {
        $count = 0;
        foreach ($player->getInventory()->getContents() as $item){
            if ($item instanceof Item){

                if ($item->getId() == $id){

                    $count += $item->getCount();

                }
            }
        }
        return $count;
    }

}