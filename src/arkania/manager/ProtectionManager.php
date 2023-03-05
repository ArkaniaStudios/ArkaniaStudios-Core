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

use pocketmine\player\Player;

final class ProtectionManager {

    /**
     * @param Player $player
     * @param string $zoneType
     * @return bool
     */
    public static function canModifyZone(Player $player, string $zoneType = 'spawn'): bool {
        $spawn = [
            'minX' => 100,
            'minZ' => -100,
            'maxX' => -100,
            'maxZ' => 100
        ];
        $warzone = [
            'minX' => 200,
            'minZ' => -200,
            'maxX' => -200,
            'maxZ' => 200
        ];
        if (strtolower($zoneType) === 'spawn')
            if ($player->getPosition()->getX() >= $spawn['minX'] || $player->getPosition()->getX() <= $spawn['maxX'] || $player->getPosition()->getZ() >= $spawn['minZ'] || $player->getPosition()->getZ() <= $spawn['maxZ'])
                return true;
            else
                return false;
        elseif(strtolower($zoneType) === 'warzone')
            if ($player->getPosition()->getX() >= $warzone['minX'] || $player->getPosition()->getX() <= $warzone['maxX'] || $player->getPosition()->getZ() >= $warzone['minZ'] || $player->getPosition()->getZ() <= $warzone['maxZ'])
                return true;
            else
                return false;
        else
            return true;
    }
}