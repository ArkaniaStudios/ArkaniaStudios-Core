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
use arkania\utils\trait\Date;
use JsonException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;

final class HomeManager {
    use Date;

    /** @var Config */
    private Config $config;

    /** @var string */
    private string $player;

    public function __construct($player) {
        $this->config = new Config(Core::getInstance()->getDataFolder() . 'homes/' . $player . '.json', Config::JSON);
        $this->player = $player;
    }

    /**
     * @param string $homeName
     * @param Position $position
     * @return void
     * @throws JsonException
     */
    public function setHome(string $homeName ,Position $position): void {
        $config = $this->config;
        $config->setNested($homeName . '.x', $position->getX());
        $config->setNested($homeName . '.y', $position->getY());
        $config->setNested($homeName . '.z', $position->getZ());
        $config->setNested($homeName . '.world', $position->getWorld()->getFolderName());
        $config->setNested($homeName . '.creation', $this->dateFormat());
        $config->setNested($homeName . '.name', $homeName);
        $config->save();
    }

    /**
     * @param string $homeName
     * @return void
     * @throws JsonException
     */
    public function delHome(string $homeName): void {
        $config = $this->config;
        $config->remove($homeName);
        $config->save();
    }

    /**
     * @param string $homeName
     * @return void
     */
    public function teleportHome(string $homeName): void {
        $player = Server::getInstance()->getPlayerExact($this->player);
        if ($player instanceof Player){
            $config = $this->config;
            $player->teleport(new Position($config->getNested($homeName.'.x'), $config->getNested($homeName . '.y'), $config->getNested($homeName . '.z'), Server::getInstance()->getWorldManager()->getWorldByName($config->getNested($homeName . '.world'))));
        }
    }

    public function existHome(string $homeName): bool {
        return $this->config->exists($homeName);
    }

    /**
     * @return int
     */
    public function countHome(): int {
        return count($this->config->getAll());
    }

    /**
     * @param string $player
     * @return Config
     */
    public function getHomeAdmin(string $player): Config {
        return new Config(Core::getInstance()->getDataFolder() . 'homes/' . $player . '.json', Config::JSON);
    }

    /**
     * @param string $player
     * @param string $homeName
     * @return Position
     */
    public function teleportAtPlayerHome(string $player, string $homeName): Position {
        $home = $this->getHomeAdmin($player);

        return new Position($home->getNested($homeName . '.x'), $home->getNested($homeName . '.y'), $home->getNested($homeName . '.z'), Server::getInstance()->getWorldManager()->getWorldByName($home->getNested($homeName . '.world')));
    }

    /**
     * @return array|string[]
     */
    public function getAllHome(): array {
        return $this->config->getAll();
    }

}