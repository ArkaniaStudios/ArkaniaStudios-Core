<?php

declare(strict_types=1);

namespace arkania\events\players;

use arkania\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;

class PlayerInteractEvent implements Listener {

    public function onPlayerInteract(\pocketmine\event\player\PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemName = $item->getCustomName();
        $name = '(§e';

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

    }

}