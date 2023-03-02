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
use pocketmine\player\Player;

final class TeleportManager {

    /** @var array */
    public static array $teleportRequest = [];

    /**
     * @param Player $target
     * @param Player $requester
     * @return void
     */
    public function sendTpaRequest(Player $target, Player $requester): void{
        self::$teleportRequest[$target->getName()] = ['time' => time(), 'teleportee' => $requester->getName()];
        $target->sendMessage(Utils::getPrefix() . "§c" . $requester->getName() . "§f veut se téléporter sur vous :\n§f- §a/tpaccept §7-> §fpour accepter,\n- §c/tpdeny §7-> §fpour refuser.");
    }

    /**
     * @param Player $target
     * @param Player $requester
     * @return void
     */
    public function sendTpaHereRequest(Player $target, Player $requester): void{
        self::$teleportRequest[$target->getName()] = ['time' => time(), 'teleportee' => $target->getName(), 'teleport' => $requester->getName()];
        $target->sendMessage(Utils::getPrefix() . "§c" . $requester->getName() . "§f veut que vous vous téléportiez sur lui :\n§f- §a/tpaccept §7-> §fpour accepter,\n- §c/tpdeny §7-> §fpour refuser.");
    }

    /**
     * @param Player $player
     * @return void
     */
    public function denyTpaRequest(Player $player): void{
        if ($this->teleporteeStillOnline($player)){
            $this->getTeleportee($player)->sendMessage(Utils::getPrefix() . "§c" . $player->getName() . "§f vient de refuser votre demande de téléportation.");
        }
        unset(self::$teleportRequest[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function teleporteeStillOnline(Player $player): bool
    {
        if (isset(self::$teleportRequest[$player->getName()])) {
            if ($this->getTeleportee($player) == null) {
                $this->updateRequest($player);
                unset(self::$teleportRequest[$player->getName()]);
            }
            return $this->getTeleportee($player) !== null;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return Player|null
     */
    public function getTeleportee(Player $player): ?Player
    {
        if (isset(self::$teleportRequest[$player->getName()])) {
            if (isset(self::$teleportRequest[$player->getName()]['teleport'])) {
                return Core::getInstance()->getServer()->getPlayerExact(self::$teleportRequest[$player->getName()]['teleport']);
            } else {
                return Core::getInstance()->getServer()->getPlayerExact(self::$teleportRequest[$player->getName()]['teleportee']);
            }
        }
        return null;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isTpaHereRequest(Player $player): bool
    {
        if (isset(self::$teleportRequest[$player->getName()]['teleport'])) {
            return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    private function updateRequest(Player $player): void
    {
        if (isset(self::$teleportRequest[$player->getName()])) {
            if (self::$teleportRequest[$player->getName()]['time'] + 30 <= time()) {
                if (($teleportee = $this->getTeleportee($player)) !== null) {
                    $teleportee->sendMessage(Utils::getPrefix() . "§cLa demande de téléportation a expiré.");
                }
                $player->sendMessage(Utils::getPrefix() . "§cLa demande de téléportation a expiré.");
                unset(self::$teleportRequest[$player->getName()]);
            }
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasRequest(Player $player): bool {
        $this->updateRequest($player);
        return isset(self::$teleportRequest[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function destroyRequest(Player $player): void {
        unset(self::$teleportRequest[$player->getName()]);
    }

}