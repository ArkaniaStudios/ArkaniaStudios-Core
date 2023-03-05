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
use mysqli;

final class EconomyManager{
    use Provider;

    /**
     * @return MySQLi
     */
    private static function getDataBase(): MySQLi {
        return (new EconomyManager)->getProvider();
    }

    /**
     * @return void
     */
    public static function init(): void {
        $db = self::getDataBase();
        $db->query("CREATE TABLE IF NOT EXISTS money(name VARCHAR(20), money_count INT)");
        $db->close();
    }

    /**
     * @param $playerName
     * @return int|bool|string
     */
    public function getMoney($playerName): int|bool|string {
        $db = self::getDataBase()->query("SELECT money_count FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $money = $db->fetch_array()[0] ?? 0;
        $db->close();
        return $money;
    }
    /**
     * @param $playerName
     * @param int $amount
     * @return void
     */
    public function addMoney($playerName, int $amount): void {
        $db = self::getDataBase()->query("SELECT * FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE money SET money_count= money_count + '$amount' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        else
            Query::query("INSERT INTO money(name, money_count) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '$amount')");
    }

    /**
     * @param $playerName
     * @param int $amount
     * @return void
     */
    public function delMoney($playerName, int $amount): void {
        $db = self::getDataBase()->query("SELECT * FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE money SET money_count= money_count - '$amount' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        else
            return;
    }

    /**
     * @param $playerName
     * @param int $amount
     * @return void
     */
    public function setMoney($playerName, int $amount): void {
        $db = self::getDataBase()->query("SELECT * FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE money SET money_count='$amount' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        else
            Query::query("INSERT INTO money(name, money_count) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '$amount')");
    }

    /**
     * @param $playerName
     * @return void
     */
    public function resetMoney($playerName): void {
        $db = self::getDataBase()->query("SELECT * FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE money SET money_count='1000' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        else
            Query::query("INSERT INTO money(name, money_count) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '1000')");
    }

    /**
     * @param $playerName
     * @return bool
     */
    public function hasAccount($playerName): bool {
        $db = self::getDataBase()->query("SELECT * FROM money WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        return $db->num_rows > 0;
    }

    public function getAllMoney(): array {
        $res = self::getDatabase()->query("SELECT * FROM money");
        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[1];
        }
        $res->close();
        return $ret;
    }

}