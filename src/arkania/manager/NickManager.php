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
use pocketmine\player\Player;

final class NickManager {

    /** @var array */
    private array $realName = [];

    /** @var array */
    private array $nickName = [];

    /** @var array */
    private array $ranks = [];

    /**
     * @param Player $player
     * @param string $newName
     * @return void
     */
    public function setPlayerNick(Player $player, string $newName): void {
        if (isset($this->ranks[$player->getName()]))
            $this->removePlayerNick($player);
        $this->realName[$newName] = $player->getName();
        $this->nickName[$player->getName()] = $newName;
        $this->ranks[$player->getName()] = Core::getInstance()->getRanksManager()->getPlayerRank($player->getName());
        Core::getInstance()->getRanksManager()->addPlayerPermission($player->getName(), 'arkania:permission.nick');
        Core::getInstance()->getRanksManager()->setRank($player->getName(), 'Joueur');
        Core::getInstance()->getRanksManager()->updateNameTag($player, $newName);
        $player->setDisplayName($newName);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayerNick(Player $player): void {
        $realName = $this->getRealName($this->getNickName($player));
        $player->setDisplayName($realName);
        Core::getInstance()->getRanksManager()->updateNameTag($player, $realName);
        Core::getInstance()->getRanksManager()->setRank($player->getName(), $this->ranks[$player->getName()]);
        Core::getInstance()->getRanksManager()->delPlayerPermission($player->getName(), 'arkania:permission.nick');
        if (isset($this->ranks[$player->getName()])){
            unset($this->realName[$this->getNickName($player)]);
            unset($this->nickName[$player->getName()]);
            unset($this->ranks[$player->getName()]);
        }
    }

    /**
     * @param string $nickName
     * @return string
     */
    public function getRealName(string $nickName): string {
        return $this->realName[$nickName];
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getNickName(Player $player): string{
        return $this->nickName[$player->getName()];
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isNick(Player $player): bool {
        return isset($this->nickName[$player->getName()]);
    }

}