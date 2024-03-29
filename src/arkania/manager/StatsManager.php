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

use arkania\data\DataBaseConnector;
use arkania\utils\Query;
use arkania\utils\Utils;
use mysqli;
use pocketmine\player\Player;

final class StatsManager {

    /**
     * @return mysqli
     */
    private static function getDatabase(): MySQLi {
        return new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
    }

    /**
     * @return void
     */
    public static function init(): void {
        $db = self::getDatabase();
        $db->query("CREATE TABLE IF NOT EXISTS inscription(name VARCHAR(20), inscription TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS kills(name VARCHAR(20), killCount INT)");
        $db->query("CREATE TABLE IF NOT EXISTS deaths(name VARCHAR(20), DeathCount INT)");
        $db->query("CREATE TABLE IF NOT EXISTS player_time(name VARCHAR(20), time FLOAT)");
        $db->query("CREATE TABLE IF NOT EXISTS player_number(number INT)");
        $db->query("CREATE TABLE IF NOT EXISTS player_connection(name VARCHAR(20), server TEXT)");
        $db->close();
    }

    /** @var array */
    public static array $time = [];

    /**
     * @param Player $player
     * @param $date
     * @return void
     */
    public function setInscription(Player $player, $date): void {
        $player = $player->getName();
        $player = strtolower($player);
        $db = self::getDataBase();
        Query::query("INSERT INTO  inscription(name, inscription) VALUES ('" . self::getDatabase()->real_escape_string($player) . "', '$date');");
        $db->close();
    }

    /**
     * @param string $player
     * @return false|mixed
     */
    public function getInscription(string $player): mixed {
        $player = strtolower($player);
        $data = self::getDataBase()->query("SELECT inscription FROM inscription WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $date = $data->fetch_array()[0] ?? false;
        $data->close();
        return $date;
    }



    /**
     * @param Player $player
     * @return false|mixed
     */
    public function getPlayerKill(Player $player): mixed {
        $player = $player->getName();
        $player = strtolower($player);
        $data = self::getDataBase()->query("SELECT killCount FROM kills WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $kills = $data->fetch_array()[0] ?? false;
        $data->close();
        return $kills;
    }

    /**
     * @param Player $player
     * @param $amount
     * @return void
     */
    public function addPlayerKill(Player $player, $amount): void {
        $player = $player->getName();
        $player = strtolower($player);
        $db = self::getDataBase();
        Query::query("UPDATE kills SET killCount = killCount + $amount WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $db->close();
    }

    /**
     * @param Player $player
     * @return false|mixed
     */
    public function getPlayerDeath(Player $player): mixed {
        $player = $player->getName();
        $player = strtolower($player);
        $data = self::getDataBase()->query("SELECT deathCount FROM deaths WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $kills = $data->fetch_array()[0] ?? false;
        $data->close();
        return $kills;
    }

    /**
     * @param Player $player
     * @param $amount
     * @return void
     */
    public function addPlayerDeath(Player $player, $amount): void {
        $player = $player->getName();
        $player = strtolower($player);
        $db = self::getDataBase();
        Query::query("UPDATE deaths SET deathCount = deathCount + $amount WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $db->close();
    }

    /**
     * @return array
     */
    public function getAllKill(): array {
        $res = self::getDatabase()->query("SELECT * FROM kills");
        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[1];
        }
        $res->close();
        return $ret;
    }

    /**
     * @return array
     */
    public function getAllDeath(): array {
        $res = self::getDatabase()->query("SELECT * FROM deaths");
        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[1];
        }
        $res->close();
        return $ret;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function createPlayerStats(Player $player): void {
        $name = strtolower($player->getName());
        $db = self::getDatabase()->query("SELECT * FROM kills WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $coins = $db->num_rows > 0;
        if(!$coins){
            self::getDatabase()->query("INSERT INTO kills (name, killCount) VALUES ('" . self::getDatabase()->real_escape_string($name) . "', 0);");
        }
        $db->close();
        $db = self::getDatabase()->query("SELECT * FROM deaths WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $coins = $db->num_rows > 0;
        if(!$coins){
            self::getDatabase()->query("INSERT INTO deaths (name, deathCount) VALUES ('" . self::getDatabase()->real_escape_string($name) . "', 0);");
        }
        $db->close();
    }

    /**
     * @return void
     */
    public function addPlayerCount(): void {
        $db = self::getDatabase()->query("SELECT * FROM player_number");
        $number = $db->num_rows > 0;
        if (!$number)
            Query::query("INSERT INTO player_number(number) VALUES ('1')");
        else
            Query::query("UPDATE player_number SET number=number + '1'");
        $db->close();
    }

    /**
     * @return int
     */
    public function getPlayerRegister(): int {
        $db = self::getDatabase()->query("SELECT * FROM player_number");
        $number = $db->fetch_array()[0] ?? 0;
        $db->close();
        return (int)$number;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function setServerConnection(Player $player): void {
        $db = self::getDatabase()->query("SELECT * FROM player_connection WHERE name='" . $player->getName() . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE player_connection SET server='§aEn ligne §f(§e" . Utils::getServerName() . "§f)' WHERE name='" . $player->getName() . "'");
        else
            Query::query("INSERT INTO player_connection(name, server) VALUES ('" . $player->getName() . "', '" . Utils::getServerName() . "')");
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removeServerConnection(Player $player): void {
        Query::query("UPDATE player_connection SET server='§cHors ligne' WHERE name='" . $player->getName() . "'");
    }

    /**
     * @param $playerName
     * @return string
     */
    public function getServerConnection($playerName): string {
        $db = self::getDatabase()->query("SELECT server FROM player_connection WHERE name='" . $playerName . "'");
        $result = $db->fetch_array()[0] ?? 'Error';
        $db->close();
        return $result;
    }

    /**
     * @param $player
     * @return string
     */
    public function getTime($player): string {
        return 'Désactivé';
    }
}