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

namespace arkania\utils;

use arkania\Core;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

final class Utils {

    /**
     * @return string
     */
    public static function getPrefix(): string {
        return Core::getInstance()->getConfig()->get('prefix');
    }

    /**
     * @param string $value
     * @return bool|int
     */
    public static function isValidArgument(string $value): bool|int {
        return preg_match('/^[A-Za-z0-9_]+$/', $value);
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValidNumber(string $value): bool {
        return is_numeric($value) && $value > 0;
    }
    /**
     * @return string
     */
    public static function getServerName(): string
    {
        $port = Server::getInstance()->getPort();
        if ($port === 1000)
            return 'Arkania-V2';
        elseif($port === 19133)
            return 'Lobby1';
        elseif ($port === 19134)
            return 'Theta';
        elseif ($port === 19135)
            return 'Zeta';
        elseif($port === 19136)
            return 'Minage1';
        elseif($port === 19137)
            return 'Minage2';
        elseif($port === 19138)
            return 'Minage3';
        return 'unknown';
    }

    /**
     * @param $value
     * @return string
     */
    public static function removeColorOnMessage($value): string {
        return str_replace(['§1', '§2', '§3', '§4', '§5', '§6', '§7', '§8', '§9', '§0', '§a', '§e', '§b', '§c', '§g', '§', '§r', '§f', '§o', '§l', '§k', '§ff'], '', $value);
    }

}