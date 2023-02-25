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

namespace arkania\inventory;

use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use arkania\libs\muqsit\invmenu\type\InvMenuType;
use arkania\libs\muqsit\invmenu\type\util\InvMenuTypeBuilders;
use pocketmine\block\inventory\CraftingTableInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class CraftingTableTypeInventory implements InvMenuType {

    private InvMenuType $type;

    public function __construct() {
        $this->type = InvMenuTypeBuilders::BLOCK_FIXED()
            ->setBlock(VanillaBlocks::CRAFTING_TABLE())
            ->setSize(9)
            ->setNetworkWindowType(WindowTypes::WORKBENCH)
            ->build();
    }

    public function createGraphic(InvMenu $menu, Player $player): ?InvMenuGraphic {
        return $this->type->createGraphic($menu, $player);
    }

    public function createInventory(): Inventory {
        return new CraftingTableInventory(Position::fromObject(Vector3::zero(), null));
    }

}