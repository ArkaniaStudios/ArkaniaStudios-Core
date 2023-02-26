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

namespace arkania\factions\claims;

use arkania\Core;
use pocketmine\world\World;

class Claim {

    public function __construct(private string $faction, private int $chunkX, private int $chunkZ, private string $world) {
    }

    /**
     * @return string
     */
    public function getFaction(): string {
        return $this->faction;
    }

    /**
     * @return World|null
     */
    public function getLevel(): ?World {
        return Core::getInstance()->getServer()->getWorldManager()->getWorldByName($this->world);
    }

    /**
     * @return int
     */
    public function getChunkX(): int {
        return $this->chunkX;
    }

    /**
     * @return int
     */
    public function getChunkZ(): int {
        return $this->chunkZ;
    }

}