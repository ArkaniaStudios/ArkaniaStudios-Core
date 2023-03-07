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
use JsonException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

final class SpawnManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Position $position
     * @return void
     * @throws JsonException
     */
    public function setServerSpawn(Position $position): void {
        $config = $this->core->config;
        $config->setNested('spawn.x', $position->getX());
        $config->setNested('spawn.y', $position->getY());
        $config->setNested('spawn.z', $position->getZ());
        $config->setNested('spawn.world', $position->getWorld()->getFolderName());
        $config->save();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function teleportSpawn(Player $player): void {
        $config = $this->core->config;
        $x = $config->getNested('spawn.x');
        $y = $config->getNested('spawn.y');
        $z = $config->getNested('spawn.z');
        $world = $config->getNested('spawn.world');
        Server::getInstance()->getWorldManager()->loadWorld($world);
        $player->teleport(new Position($x, $y, $z, $this->core->getServer()->getWorldManager()->getWorldByName($world)));
    }

    /**
     * @return bool
     */
    public function existSpawn(): bool {
        $config = $this->core->config;
        return $config->exists('spawn');
    }
}