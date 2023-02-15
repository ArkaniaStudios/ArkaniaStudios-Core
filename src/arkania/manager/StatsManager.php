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
use pocketmine\player\Player;

final class StatsManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

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
        $db->close();
    }

    /** @var array */
    public static array $time = [];

    /** @var array */
    public static array $jointime = [];

    /**
     * @param Player $player
     * @param $date
     * @return void
     */
    public function setInscription(Player $player, $date): void {
        $player = $player->getName();
        $player = strtolower($player);
        $db = self::getDataBase();
        self::getDatabase()->query("INSERT INTO  player_time(name, inscription) VALUES ('" . self::getDatabase()->real_escape_string($player) . "', '$date');");
        $db->close();
    }

    /**
     * @param Player $player
     * @return false|mixed
     */
    public function getInscription(Player $player): mixed {
        $player = $player->getName();
        $player = strtolower($player);
        $data = self::getDataBase()->query("SELECT inscription FROM inscription WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $date = $data->fetch_array()[0] ?? false;
        $data->close();
        return $date;
    }

    /**
     * @param Player $player
     * @return mixed
     */
    public function getTime(Player $player): mixed {
        $player = strtolower($player->getName());
        return self::$time[$player];
    }

    /**
     * @return array
     */
    public function getAllTime(): array {
        $res = self::getDataBase()->query("SELECT * FROM player_time");

        $ret = [];
        foreach ($res->fetch_all() as $value){
            $ret[$value[0]] = $value[1];
        }
        $res->close();
        return $ret;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function createTime(Player $player): void {
        $name = strtolower($player->getName());
        $db = self::getDatabase()->query("SELECT * FROM player_time WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $time = $db->num_rows > 0;
        if(!$time){
            $path = $this->core->playertime;
            if($path->exists($name)){
                $time = $path->get($name);
            } else {
                $time = 0;
            }
            self::getDatabase()->query("INSERT INTO player_time (name, time) VALUES ('" . self::getDatabase()->real_escape_string($name) . "', '$time');");
        }
        $db->close();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function synchroJoinStats(Player $player): void {
        $name = strtolower($player->getName());
        $data = self::getDatabase()->query("SELECT time FROM player_time WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $time = $data->fetch_array()[0] ?? false;
        self::$time[strtolower($player->getName())] = $time;
        self::$jointime[strtolower($player->getName())] = time();
        $data->close();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function synchroQuitStats(Player $player): void {
        $name = strtolower($player->getName());
        $time = self::$time[$name];
        $newtime = $time + (time() - self::$jointime[$name]);
        $db = self::getDatabase();
        Query::query("UPDATE player_time SET time = '$newtime' WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $db->close();
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
     * @param $player
     * @return void
     */
    public function createPlayerStats($player): void {
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        $player = strtolower($player);
        $db = self::getDatabase()->query("SELECT * FROM kills WHERE name='" . self::getDatabase()->real_escape_string($player) . "'");
        $coins = $db->num_rows > 0;
        if(!$coins){
            self::getDatabase()->query("INSERT INTO kills (name, killCount) VALUES ('" . self::getDatabase()->real_escape_string($player) . "', 0);");
        }
        $db->close();
        $db = self::getDatabase()->query("SELECT * FROM deaths WHERE name='" . self::getDatabase()->real_escape_string($player) . "'");
        $coins = $db->num_rows > 0;
        if(!$coins){
            self::getDatabase()->query("INSERT INTO deaths (name, deathCount) VALUES ('" . self::getDatabase()->real_escape_string($player) . "', 0);");
        }
        $db->close();
    }
}