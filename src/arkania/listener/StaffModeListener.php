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

namespace arkania\listener;

use arkania\Core;
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

final class StaffModeListener implements Listener {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param PlayerDropItemEvent $event
     * @return void
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();

        if ($this->core->getStaffManager()->isInStaffMode($player))
            $event->cancel();
    }

    /**
     * @param InventoryTransactionEvent $event
     * @return void
     */
    public function onItemTransaction(InventoryTransactionEvent $event): void {
        $player = $event->getTransaction()->getSource();
        if ($this->core->getStaffManager()->isInStaffMode($player))
            $event->cancel();
    }

    /**
     * @param EntityItemPickupEvent $event
     * @return void
     */
    public function onEntityItemPickup(EntityItemPickupEvent $event): void {
        $player = $event->getOrigin();

        if ($player instanceof Player)
            if ($this->core->getStaffManager()->isInStaffMode($player))
                $event->cancel();
    }

    /**
     * @param PlayerExhaustEvent $event
     * @return void
     */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();

        if ($player instanceof Player)

            if ($this->core->getStaffManager()->isInStaffMode($player))
                $event->cancel();
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $player = $event->getEntity();

        if ($player instanceof Player)
            if ($this->core->getStaffManager()->isInStaffMode($player))
                $event->cancel();
    }

    /**
     * @param PlayerItemUseEvent $event
     * @return void
     */
    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($this->core->getStaffManager()->isInStaffMode($player)) {
            if ($item->getId() === 351){
                if ($item->getMeta() === 10) {
                    $this->core->getStaffManager()->removeVanish($player);
                    $player->getInventory()->setItem(0, VanillaItems::GRAY_DYE()->setCustomName('§c- §fVanish §c-'));
                }
                if ($item->getMeta() === 8) {
                    $this->core->getStaffManager()->setVanish($player);
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

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $player = $event->getDamager();
        $target = $event->getEntity();

        if ($player instanceof Player && $target instanceof Player){
            if ($this->core->getStaffManager()->isInStaffMode($player)){
                $event->cancel();
                $item = $player->getInventory()->getItemInHand()->getId();
                if ($item === VanillaBlocks::ICE()->asItem()->getId()){
                    if ($this->core->getStaffManager()->isFreeze($target)){
                        $this->core->getStaffManager()->setFreeze($target, false);
                        $target->sendMessage(Utils::getPrefix() . "§aVous n'êtes plus gelé !");
                        $this->core->getRanksManager()->updateNameTag($target);
                        $player->sendMessage(Utils::getPrefix() . "§aVous avez dégelé " . RanksManager::getRanksFormatPlayer($target) . "§a.");
                    }else{
                        $this->core->getStaffManager()->setFreeze($target);
                        $target->setNameTag("[§bFREEZE§f] " . $target->getName());
                        $target->sendMessage(Utils::getPrefix() . "§cVous avez été gelé par " . RanksManager::getRanksFormatPlayer($player) . "§c. Merci de suivre les indications qui vont vous êtes données.");
                        $target->sendTitle("§c§lFREEZE", "§r§cMerci de regarder votre chat !", 100, 100, 100);
                        $player->sendMessage(Utils::getPrefix() . "§aVous avez bien gelé " . RanksManager::getRanksFormatPlayer($target) . "§a.");
                    }
                }elseif($item === VanillaItems::BOOK()->getId()){
                    $stats = $this->core->getStatsManager();
                    $faction = $this->core->getFactionManager();
                    $player->sendMessage(Utils::getPrefix() . "Voici les informations concernant " . RanksManager::getRanksFormatPlayer($target) . "§f:" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->getRanksManager()->getRankColor($target->getName()) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($target->getName()) . PHP_EOL . "§f- Argent : §e" . $this->core->getEconomyManager()->getMoney($target->getName()) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($target->getName()) . PHP_EOL . "§f- Temps de jeu : §e" . $this->tempsFormat($stats->getTime($target->getName())) . PHP_EOL . PHP_EOL . "§f- Status : " . $this->core->getStatsManager()->getServerConnection($target->getName()));
                }elseif($item === VanillaItems::STONE_AXE()->getId()){
                    $this->core->getFormManager()->sendBanUiForm($player, $target);
                }
            }
        }
    }
}