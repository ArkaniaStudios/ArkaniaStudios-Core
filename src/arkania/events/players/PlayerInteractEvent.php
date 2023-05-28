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

namespace arkania\events\players;

use arkania\Core;
use arkania\manager\ProtectionManager;
use arkania\utils\Utils;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;

final class PlayerInteractEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }



    /**
     * @param \pocketmine\event\player\PlayerInteractEvent $event
     * @return void
     */
    public function onPlayerInteract(\pocketmine\event\player\PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemName = $item->getCustomName();
        $name = '§e(';
        $block = $event->getBlock();

        if (ProtectionManager::isInProtectedZone($block->getPosition(), 'warzone') && !$player->getServer()->isOp($player->getName())) {
            if ($player->getInventory()->getItemInHand()->getId() == VanillaItems::BUCKET()->getId() or $player->getInventory()->getItemInHand()->getId() === VanillaItems::FLINT_AND_STEEL()->getId()) {
                $event->cancel();
            }
        }

        if($block->getId() === BlockLegacyIds::ENCHANTMENT_TABLE){
            $event->cancel();
            $this->core->enchantTableForm->sendEnchantTable($player);
        }

        if ($item->getId() == VanillaItems::EXPERIENCE_BOTTLE()->getId()){
            $event->cancel();
            if (str_contains($itemName, $name)){
                $xpLevel = explode("§fBouteille d'xp (", $itemName);
                $xpLevel = explode('§f)', $xpLevel[1]);
                $xpLevel = intval($xpLevel[0]);
                $player->getXpManager()->addXpLevels($xpLevel);
                $itemremove = ItemFactory::getInstance()->get($item->getId(), $item->getMeta(), $item->getCount() - 1);
                $itemremove->setCustomName($itemName);
                $player->getInventory()->setItemInHand($itemremove);
                $player->sendMessage(Utils::getPrefix() . "Vous avez reçu §e$xpLevel expérience(s) §fgrace à votre bouteille.");
            }else{
                $player->getXpManager()->addXp(mt_rand(3, 11));
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get($item->getID(), $item->getMeta(), $item->getCount() - 1));
            }
        }
        if ($item->getId() == VanillaItems::PAPER()->getId()){
            $event->cancel();

            if (str_contains($itemName, $name)){
                $money = explode("§fBillet §e(", $itemName);
                $money = explode(")", $money[1]);
                $money = intval($money[0]);
                $this->core->getEconomyManager()->addMoney($player->getName(), $money);
                $itemremove = ItemFactory::getInstance()->get($item->getId(), $item->getMeta(), $item->getCount() - 1);
                $itemremove->setCustomName($itemName);
                $player->getInventory()->setItemInHand($itemremove);
                $player->sendMessage(Utils::getPrefix() . "Vous avez reçu §e$money  §fgrace à votre billet.");
            }
        }
    }
}