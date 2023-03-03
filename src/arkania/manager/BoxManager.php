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
use JsonException;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class BoxManager {

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
}