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
use arkania\data\DataBaseConnector;
use arkania\utils\Query;
use mysqli;

final class SanctionManager {

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
        $db->query("CREATE TABLE IF NOT EXISTS ban(name VARCHAR(20), staff TEXT, temps INT, raison TEXT, server TEXT, date TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS mute(name VARCHAR(20), staff TEXT, temps INT, raison TEXT, server TEXT, date TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS warn(name VARCHAR(20), value TEXT)");
        $db->close();
    }

    /** @var Core */
    private Core $core;

    /**
     * @param Core $core
     */
    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param string $playerName
     * @param string $staff
     * @param int $time
     * @param string $raison
     * @param string $server
     * @param string $date
     * @return void
     */
    public function addBan(string $playerName, string $staff, int $time, string $raison, string $server, string $date): void {
        $db = self::getDataBase();
        Query::query("INSERT INTO ban(name,
                staff,
                temps,
                raison,
                server,
                date
                )VALUES (
                         '$playerName',
                         '$staff',
                         '$time',
                         '$raison',
                         '$server',
                         '$date'
                )");
        $db->close();
    }

    /**
     * @param string $playerName
     * @return bool
     */
    public function isBan(string $playerName): bool {
        $db = self::getDataBase()->query("SELECT * FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            return true;
        else
            return false;
    }

    /**
     * @param string $playerName
     * @return int
     */
    public function getBanTime(string $playerName): int {
        $db = self::getDataBase()->query("SELECT temps FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (int)$result;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getBanRaison(string $playerName): string {
        $db = self::getDataBase()->query("SELECT raison FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (string)$result;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getBanStaff(string $playerName): string {
        $db = self::getDataBase()->query("SELECT staff FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (string)$result;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getBanServer(string $playerName): string {
        $db = self::getDataBase()->query("SELECT server FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (string)$result;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getBanIp(string $playerName): string {
        $db = self::getDataBase()->query("SELECT ip FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (string)$result;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getBanData(string $playerName): string {
        $db = self::getDataBase()->query("SELECT date FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $result = $db->fetch_array()[0] ?? false;
        return (string)$result;
    }

    /**
     * @param string $playerName
     * @return void
     */
    public function removeBan(string $playerName): void {
        $db = self::getDataBase();
        Query::query("DELETE FROM ban WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $db->close();
    }

    /**
     * @return array
     */
    public function getAllBan(): array {
        $res = self::getDatabase()->query("SELECT * FROM ban");
        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[0];
        }
        $res->close();
        return $ret;
    }

}