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

namespace arkania\manager;

use arkania\Core;
use arkania\data\WebhookData;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

final class StaffManager {
    use Webhook;

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

    /** @var array */
    private array $gamemode = [];

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function saveInventory(Player $player): void {
        $this->inventory[$player->getName()] = $player->getInventory()->getContents();
        $this->armor[$player->getName()] = $player->getArmorInventory()->getContents();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function restorInventory(Player $player): void {
        $player->getInventory()->setContents($this->inventory[$player->getName()]);
        $player->getArmorInventory()->setContents($this->armor[$player->getName()]);
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
                $onlinePlayer->sendMessage("[§c-§f] ". RanksManager::getRanksFormatPlayer($player));
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
                $onlinePlayer->sendMessage("[§a+§f] ". RanksManager::getRanksFormatPlayer($player));
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

        $this->core->getRanksManager()->updateNameTag($player);
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

        $contentInv = null;
        foreach ($player->getInventory()->getContents() as $item)
            $contentInv .= '- ' . $item . PHP_EOL;
        $contentArmor = null;
        foreach ($player->getArmorInventory()->getContents() as $armor)
            $contentArmor .= '- ' . $armor . PHP_EOL;

        $this->saveInventory($player);
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $this->sendDiscordWebhook('**STAFFMODE**', "**" . $player->getName() . "** vient de ce mettre en staff mode." . PHP_EOL . PHP_EOL . "*Contenue de son inventaire*" . PHP_EOL . $contentInv . PHP_EOL . '*Armure*' . PHP_EOL . $contentArmor, '・StaffMode Système - ArkaniaStudios', 0xFFF, WebhookData::STAFFMODE);

        $this->gamemode[$player->getName()] = $player->getGamemode()->name();
        $player->setGamemode(GameMode::ADVENTURE());
        $player->setFlying(true);
        $player->setAllowFlight(true);

        $player->getInventory()->setItem(0, VanillaItems::LIME_DYE()->setCustomName('§c- §fVanish §c-'));
        $player->getInventory()->setItem(2, VanillaItems::BOOK()->setCustomName('§c- §fPlayerInfos §c-'));
        $player->getInventory()->setItem(4, VanillaItems::COMPASS()->setCustomName('§c- §fRandomTp §c-'));
        $player->getInventory()->setItem(6, VanillaBlocks::ICE()->asItem()->setCustomName('§c- §fFreeze §c-'));
        $player->getInventory()->setItem(8, VanillaItems::STONE_AXE()->setCustomName('§c- §fSanctions §c-'));

        $this->setVanish($player);

        $this->staffmode[$player->getName()] = $player->getName();
        $player->sendMessage(Utils::getPrefix() . "§aVous êtes maintenant en staffmode.");
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removeStaffMode(Player $player): void {
        $this->restorInventory($player);

        $this->sendDiscordWebhook('**STAFFMODE**', "**" . $player->getName() . "** vient de retirer son staffmode." . PHP_EOL . PHP_EOL . "- Status de l'inventaire : **Récupéré**", 'StaffMode Système - ArkaniaStudios', 0xFEA, WebhookData::STAFFMODE);

        $player->setGamemode(GameMode::fromString($this->gamemode[$player->getName()]));
        $player->setFlying(false);
        $player->setAllowFlight(false);

        $this->removeVanish($player);

        unset($this->staffmode[$player->getName()]);
        unset($this->inventory[$player->getName()]);
        unset($this->armor[$player->getName()]);
        unset($this->gamemode[$player->getName()]);
        $player->sendMessage(Utils::getPrefix() . "§cVous n'êtes plus en StaffMode.");
    }
}