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
use pocketmine\event\player\PlayerItemUseEvent;
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
            if ($item->getNamedTag()->getTag('experience') !== null){
                $event->cancel();
                $player->sendMessage(Utils::getPrefix() . '§aVous venez de recevoir §e' . $item->getNamedTag()->getTag('experience')->getValue() . ' §aniveaux d\'expérience via votre bouteille !');
                $player->getXpManager()->addXpLevels($item->getNamedTag()->getTag('experience')->getValue());
                $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
            }
        }
        if ($item->getId() == VanillaItems::PAPER()->getId()){
            if ($item->getNamedTag()->getTag('money') !== null){
                $event->cancel();
                $player->sendMessage(Utils::getPrefix() . '§aVous venez de recevoir §e' . $item->getNamedTag()->getTag('money')->getValue() . ' §a via votre billet !');
                $this->core->getEconomyManager()->addMoney($player->getName(), $item->getNamedTag()->getTag('money')->getValue());
                $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
            }
        }
    }

    /**
     * @param PlayerItemUseEvent $event
     * @return void
     */
    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getId() == VanillaItems::EXPERIENCE_BOTTLE()->getId()){
            if ($item->getNamedTag()->getTag('experience') !== null){
                $event->cancel();
                $player->sendMessage(Utils::getPrefix() . '§aVous venez de recevoir §e' . $item->getNamedTag()->getTag('experience')->getValue() . ' §aniveaux d\'expérience via votre bouteille !');
                $player->getXpManager()->addXpLevels($item->getNamedTag()->getTag('experience')->getValue());
                $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
            }
        }
        if ($item->getId() == VanillaItems::PAPER()->getId()){
            if ($item->getNamedTag()->getTag('money') !== null){
                $event->cancel();
                $player->sendMessage(Utils::getPrefix() . '§aVous venez de recevoir §e' . $item->getNamedTag()->getTag('money')->getValue() . ' §a via votre billet !');
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$item->getNamedTag()->getTag('money')->getValue());
                $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
            }
        }
    }
}