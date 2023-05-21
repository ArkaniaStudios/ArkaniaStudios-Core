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

use pocketmine\math\Vector3;

final class ProtectionManager {

    /**
     * @param Vector3 $pos
     * @param string $type
     * @return bool
     */
    public static function isInProtectedZone(Vector3 $pos, string $type): bool {
        if($type === 'spawn') {
            $minXSpawn = -103 ;
            $maxXSpawn = 100 ;
            $minZSpawn = -104;
            $maxZSpawn = 102;
        } else {
            $minXSpawn = -511;
            $maxXSpawn = 511;
            $minZSpawn = -511;
            $maxZSpawn = 511;
        }
        return ($pos->getX() <= $maxXSpawn && $pos->getX() >= $minXSpawn) && ($pos->getZ() <= $maxZSpawn && $pos->getZ() >= $minZSpawn);
    }
}