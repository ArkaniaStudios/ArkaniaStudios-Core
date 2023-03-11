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

namespace arkania\events\animations\purif;

use arkania\Core;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class PurifAnimation {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Vector3 $position
     * @return bool
     */
    public function isInPurifZone(Vector3 $position): bool {
        $config = $this->core->purif;
        $minXSpawn = $config->getNested('x.min');
        $maxXSpawn = $config->getNested('x.max');
        $minZSpawn = $config->getNested('z.min');
        $maxZSpawn = $config->getNested('z.max');
        return ($position->getX() <= $maxXSpawn && $position->getX() >= $minXSpawn) && ($position->getZ() <= $maxZSpawn && $position->getZ() >= $minZSpawn);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasItem(Player $player): bool {
        return $player->getInventory()->contains(VanillaItems::DIAMOND());
    }
}