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
use arkania\data\DataBaseConnector;
use arkania\utils\trait\Provider;
use JsonException;
use mysqli;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class BoxManager {
    use Provider;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     * @param string $world
     * @return void
     * @throws JsonException
     */
    public function setServerBox(float $x, float $y, float $z, string $world): void {
        $config = $this->core->config;
        $config->setNested('box.x', $x);
        $config->setNested('box.y', $y);
        $config->setNested('box.z', $z);
        $config->setNested('box.world', $world);
        $config->save();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function teleportBox(Player $player): void {
        $config = $this->core->config;
        $x = $config->get('box')['x'];
        $y = $config->get('box')['y'];
        $z = $config->get('box')['z'];
        $world = $config->get('box')['world'];
        $player->teleport(new Position($x, $y, $z, $this->core->getServer()->getWorldManager()->getWorldByName($world)));
    }

    /**
     * @return bool
     */
    public function existBox(): bool {
        $config = $this->core->config;
        return $config->exists('box');
    }

    /**
     * @return void
     */
    public static function init(): void {
        $database = new mysqli(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
        $database->query("CREATE TABLE IF NOT EXISTS key(name, votekey INT, premiumkey INT)");
        $database->close();
    }

    /**
     * @param string $playerName
     * @param string $keyType
     * @param int $number
     * @return void
     */
    public function addKey(string $playerName, string $keyType, int $number): void {

    }
}