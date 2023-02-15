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

namespace arkania\utils;

use arkania\Core;
use pocketmine\Server;

final class Utils {

    /**
     * @return string
     */
    public static function getPrefix(): string {
        return Core::getInstance()->config->get('prefix');
    }

    /**
     * @param string $value
     * @return bool|int
     */
    public static function isValidArgument(string $value): bool|int {
        return preg_match('/[A-Za-z0-9]$/', $value);
    }

    /**
     * @return string
     */
    public static function getServerName(): string {
        $port = Server::getInstance()->getPort();
        if ($port === 10286)
            return 'Arkania-V2';
        elseif($port === 10297)
            return 'Theta';
        elseif($port === 10298)
            return 'Zeta';
        elseif($port === 10299)
            return 'Epsilon';
        return 'unknown';
    }

}