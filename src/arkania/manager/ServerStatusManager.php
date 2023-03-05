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

use arkania\utils\Query;
use arkania\utils\trait\Provider;
use arkania\utils\Utils;
use mysqli;

final class ServerStatusManager {
    use Provider;

    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi {
        return (new ServerStatusManager)->getProvider();
    }

    /**
     * @return void
     */
    public static function init(): void {
        $db = self::getDataBase();
        $db->query("CREATE TABLE IF NOT EXISTS server_status(serverName VARCHAR(20), status TEXT)");
        $db->close();
    }

    /**
     * @param string $serverName
     * @return string|bool
     */
    public function getServerStatus(string $serverName): string|bool {
        $db = self::getDataBase()->query("SELECT status FROM server_status WHERE serverName='" . $serverName . "'");
        $status = $db->fetch_array()[0] ?? false;
        if ($status === 'ouvert')
            return '§aOuvert';
        elseif($status === 'maintenance')
            return '§6Maintenance';
        elseif($status === 'ferme')
            return '§cFermé';
        return '§cFermé';
    }

    /**
     * @param string $key
     * @return void
     */
    public function setServerStatus(string $key): void {
        $serverName = Utils::getServerName();
        $db = self::getDataBase()->query("SELECT * FROM server_status WHERE serverName='$serverName'");
        $line = $db->num_rows > 0;
        if (!$line)
            Query::query("INSERT INTO server_status(serverName, status) VALUES ('$serverName', '$key')");
        else
            Query::query("UPDATE server_status SET status= '$key' WHERE serverName='$serverName'");
        $db->close();
    }
}