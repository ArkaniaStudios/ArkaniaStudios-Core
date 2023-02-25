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

namespace arkania\listener;

use arkania\Core;
use arkania\manager\FactionManager;
use arkania\manager\RanksManager;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class StaffModeListener implements Listener {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();

        if ($this->core->staff->isInStaffMode($player))
            $event->cancel();
    }

    public function onItemTransaction(InventoryTransactionEvent $event){
        $player = $event->getTransaction()->getSource();
        if ($this->core->staff->isInStaffMode($player))
            $event->cancel();
    }

    public function onEntityItemPickup(EntityItemPickupEvent $event){
        $player = $event->getOrigin();

        if ($player instanceof Player)
            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event){
        $player = $event->getPlayer();

        if ($player instanceof Player)

            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }

    public function onEntityDamage(EntityDamageEvent $event){
        $player = $event->getEntity();

        if ($player instanceof Player)
            if ($this->core->staff->isInStaffMode($player))
                $event->cancel();
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($this->core->staff->isInStaffMode($player)) {
            if ($item->getId() === 351){
                if ($item->getMeta() === 10) {
                    $this->core->staff->removeVanish($player);
                    $player->getInventory()->setItem(0, VanillaItems::GRAY_DYE()->setCustomName('§c- §fVanish §c-'));
                }
                if ($item->getMeta() === 8) {
                    $this->core->staff->setVanish($player);
                    $player->getInventory()->setItem(0, VanillaItems::LIME_DYE()->setCustomName('§c- §fVanish §c-'));
                }
            }
            if ($item->getId() === VanillaItems::COMPASS()->getId()){
                $onlinePlayer = $this->core->getServer()->getOnlinePlayers();
                if (count($onlinePlayer) <= 1){
                    $player->sendMessage(Utils::getPrefix() . "§cIl n'y a actuellement personne sur le serveur.");
                    return;
                }
                $random = $onlinePlayer[array_rand($onlinePlayer)];
                while($random === $player)
                    $random = $onlinePlayer[array_rand($onlinePlayer)];
                if ($random instanceof Player){
                    $player->teleport($random->getLocation());
                    $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté à " . RanksManager::getRanksFormatPlayer($random) . "§a.");
                }
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
        $player = $event->getDamager();
        $target = $event->getEntity();

        if ($player instanceof Player && $target instanceof Player){
            if ($this->core->staff->isInStaffMode($player)){
                $event->cancel();
                $item = $player->getInventory()->getItemInHand()->getId();
                if ($item === VanillaBlocks::ICE()->asItem()->getId()){
                    if ($this->core->staff->isFreeze($target)){
                        $this->core->staff->setFreeze($target, false);
                        $target->sendMessage(Utils::getPrefix() . "§aVous n'êtes plus gelé !");
                        $this->core->ranksManager->updateNameTag($target);
                        $player->sendMessage(Utils::getPrefix() . "§aVous avez dégelé " . RanksManager::getRanksFormatPlayer($target) . "§a.");
                    }else{
                        $this->core->staff->setFreeze($target);
                        $target->setNameTag("[§bFREEZE§f] " . $target->getName());
                        $target->sendMessage(Utils::getPrefix() . "§cVous avez été gelé par " . RanksManager::getRanksFormatPlayer($player) . "§c. Merci de suivre les indications qui vont vous êtes données.");
                        $target->sendTitle("§c§lFREEZE", "§r§cMerci de regarder votre chat !", 100, 100, 100);
                        $player->sendMessage(Utils::getPrefix() . "§aVous avez bien gelé " . RanksManager::getRanksFormatPlayer($target) . "§a.");
                    }
                }elseif($item === VanillaItems::BOOK()->getId()){
                    $stats = $this->core->stats;
                    $faction = new FactionManager();
                    $player->sendMessage(Utils::getPrefix() . "Voici les informations concernant " . RanksManager::getRanksFormatPlayer($target) . "§f:" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->ranksManager->getRankColor($target->getName()) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($target->getName()) . PHP_EOL . "§f- Argent : §e" . $this->core->economyManager->getMoney($target->getName()) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($target->getName()) . PHP_EOL . "§f- Temps de jeu : §e" . $this->tempsFormat($stats->getTime($target)) . PHP_EOL . PHP_EOL . "§f- Status : " . $this->core->stats->getServerConnection($target->getName()));
                }elseif($item === VanillaItems::STONE_AXE()->getId()){
                    $this->core->ui->sendBanUi($player, $target);
                }
            }
        }
    }
}