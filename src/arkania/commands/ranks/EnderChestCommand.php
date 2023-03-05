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
use arkania\inventory\FakeEnderChestInventory;
use arkania\utils\Utils;
use pocketmine\block\Barrel;
use pocketmine\block\Block;
use pocketmine\block\Chest;
use pocketmine\block\Furnace;
use pocketmine\block\ShulkerBox;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;

final class EnderChestCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('enderchest',
        'EnderChest - ArkaniaStudios',
        '/enderchest',
        ['ec']);
        $this->setPermission('arkania:permission.enderchest');
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        $this->sendEnderChest($player);
        return true;
    }

    /**
     * @param Player $player
     * @return void
     */
    private function sendEnderChest(Player $player): void {
        $position = $player->getPosition()->add(0, 2, 0);
        $bloc = $player->getWorld()->getBlock($position);
        if ($bloc instanceof Chest || $bloc instanceof Furnace || $bloc instanceof Barrel || $bloc instanceof ShulkerBox){
            $player->sendMessage(Utils::getPrefix() . "§cVous avez des contenants au dessus de vous. Merci de vous décaler.");
            return;
        }
        $bloc = VanillaBlocks::ENDER_CHEST();
        $bloc->position($player->getWorld(), $position->getFloorX(), $position->getFloorY(), $position->getFloorZ());
        self::sendFakeBlock([$player], $bloc);
        $player->setCurrentWindow(new FakeEnderChestInventory($bloc->getPosition(), $player->getEnderInventory()));
    }

    /**
     * @param array $players
     * @param Block $block
     * @return void
     */
    public static function sendFakeBlock(array $players, Block $block): void {
        $packet = UpdateBlockPacket::create(
            BlockPosition::fromVector3($block->getPosition()),
            RuntimeBlockMapping::getInstance()->toRuntimeId($block->getFullId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        );
        foreach ($players as $player){
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

}