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
use arkania\data\DataBaseConnector;
use mysqli;
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
        return preg_match('/[A-Za-z0-9_]$/', $value);
    }

    public static function isValidNumber(string $value): bool {
        return is_numeric($value) && $value > 0;
    }

    /**
     * @return string
     */
    public static function getServerName(): string
    {
        $port = Server::getInstance()->getPort();
        if ($port === 10286)
            return 'Arkania-V2';
        elseif ($port === 10297)
            return 'Theta';
        elseif ($port === 10298)
            return 'Zeta';
        elseif ($port === 10299)
            return 'Epsilon';
        return 'unknown';
    }

    /**
     * @deprecated
     * @return void
     * Never use if you don't tell Julien
     */
    public static function __debug__($key): void{
        $db = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);

        if ($key === 'faction') {
            $db->query("DROP TABLE factions");
            $db->query("DROP TABLE players_faction");
        }
    }

    /**
     * @param $value
     * @return string
     */
    public static function removeColorOnMessage($value): string {
        return str_replace(['§c', '§f', '§2', '§a', '§e', '§1', '§3', '§4', '§5', '§6', '§7', '§8', '§9', '§r'], '', $value);
    }

}