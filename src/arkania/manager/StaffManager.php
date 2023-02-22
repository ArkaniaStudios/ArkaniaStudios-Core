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

namespace arkania\manager;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

final class StaffManager {

    /** @var Core */
    private Core $core;

    /** @var array */
    private array $inventory = [];

    /** @var array */
    private array $armor = [];

    /** @var array */
    private array $staffmode = [];

     /** @var array */
    private array $vanish = [];

    /** @var array */
    private array $freeze = [];

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function saveInventory(Player $player): void {
        $this->inventory[$player->getName()] = serialize($player->getInventory()->getContents());
        $this->armor[$player->getName()] = serialize($player->getArmorInventory()->getContents());
    }

    /**
     * @param Player $player
     * @return void
     */
    public function restorInventory(Player $player): void {
        $player->getInventory()->setContents(unserialize($this->inventory[$player->getName()]));
        $player->getArmorInventory()->setContents(unserialize($this->armor[$player->getName()]));
    }

    /**
     * @param Player $player
     * @param bool $value
     * @return void
     */
    public function setFreeze(Player $player, bool $value = true): void {
        if ($value){
            $this->freeze[$player->getName()] = $player->getName();
            $player->setImmobile();
        }else{
            unset($this->freeze[$player->getName()]);
            $player->setImmobile(false);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isFreeze(Player $player): bool {
        return isset($this->freeze[$player->getName()]);
    }

    public function isInVanish(Player $player): bool {
        return isset($this->vanish[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function setVanish(Player $player): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
            if (!$onlinePlayer->hasPermission('arkania:permission.vanish')) {
                $onlinePlayer->hidePlayer($player);
                $onlinePlayer->sendMessage("[§c-§f] ". $player->getName());
                $entry = new PlayerListEntry();
                $entry->uuid = $player->getUniqueId();
                $pk = new PlayerListPacket();
                $pk->entries[] = $entry;
                $pk->type = PlayerListPacket::TYPE_REMOVE;
                $onlinePlayer->getNetworkSession()->sendDataPacket($pk);
            }else
                $onlinePlayer->showPlayer($player);
        }

        $player->setNameTag('[§cVanish§f] ' . $player->getName());
        $this->vanish[$player->getName()] = $player->getName();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removeVanish(Player $player): void {

        foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer){
            if (!$onlinePlayer->hasPermission('arkania:permission.vanish')) {
                $onlinePlayer->showPlayer($player);
                $onlinePlayer->sendMessage("[§a+§f] ". $player->getName());
                $pk = new PlayerListPacket();
                $pk->type = PlayerListPacket::TYPE_ADD;
                $pk->entries[] = PlayerListEntry::createAdditionEntry(
                    $player->getUniqueId(),
                    $player->getId(),
                    $player->getDisplayName(),
                    SkinAdapterSingleton::get()->toSkinData($player->getSkin()),
                    $player->getXuid()
                );
                $onlinePlayer->getNetworkSession()->sendDataPacket($pk);
            }
        }

        $player->setNameTag('[§cVanish§f] ' . $player->getName());
        unset($this->vanish[$player->getName()]);
    }


    /**
     * @param Player $player
     * @return bool
     */
    public function isInStaffMode(Player $player): bool {
        return isset($this->staffmode[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addStaffMode(Player $player): void {

        $this->saveInventory($player);

        $player->setGamemode(GameMode::ADVENTURE());
        $player->setFlying(true);
        $player->setAllowFlight(true);

        $this->setVanish($player);

        $this->staffmode[$player->getName()] = $player->getName();
        $player->sendMessage(Utils::getPrefix() . "Vous êtes maintenant en staffmode.");
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removeStaffMode(Player $player): void {
        $this->saveInventory($player);

        $player->setGamemode(GameMode::ADVENTURE());
        $player->setFlying(false);
        $player->setAllowFlight(false);

        $this->removeVanish($player);

        unset($this->staffmode[$player->getName()]);
        $player->sendMessage(Utils::getPrefix() . "Vous êtes maintenant en staffmode.");
    }
}