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

use arkania\data\DataBaseConnector;
use arkania\utils\Query;
use arkania\utils\Utils;
use mysqli;

final class ServerStatusManager {

    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi {
        return new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
    }

    /**
     * @return void
     */
    public static function init(): void {
        $db = self::getDataBase();
        $db->query("CREATE TABLE IF NOT EXISTS server_status(serverName VARCHAR(20), status TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS debug(isDebug BOOL)");
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
        elseif($status === 'maintenant')
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