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
use arkania\data\SettingsNameIds;
use arkania\utils\Query;
use mysqli;
use pocketmine\player\Player;

final class SettingsManager {

    /** @var Core */
    private Core $core;

    /** @var Player */
    private Player $player;

    public function __construct(Core $core, Player $player) {
        $this->core = $core;
        $this->player = $player;
    }

    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi {
        return new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
    }

    public static function init(): void {
        $db = self::getDataBase();
        $db->query("CREATE TABLE IF NOT EXISTS settings(name VARCHAR(20), allowTp BOOL, allowMsg BOOL, clearLagMessage BOOL)");
        $db->close();
    }

    /**
     * @return void
     */
    public function createUserSettings(): void {
        $db = self::getDataBase()->query("SELECT * FROM settings WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) ."'");
        $settings = $db->num_rows > 0;
        if (!$settings)
            self::getDataBase()->query("INSERT INTO settings(name, allowTp, allowMsg, clearLagMessage) VALUES ('" . self::getDataBase()->real_escape_string($this->player->getName()) . "', 'true', 'true', 'true')");
        $db->close();
    }

    /**
     * @param $key
     * @return false|mixed
     */
    public function getSettings($key): mixed {
        $db = self::getDataBase()->query("SELECT $key FROM settings WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) ."'");
        $settings = $db->fetch_array()[0] ?? false;
        $db->close();
        return $settings;
    }

    /**
     * @param $key
     * @param bool $value
     * @return void
     */
    public function setSettings($key, bool $value): void {
        $db = self::getDataBase();
        Query::query("UPDATE settings SET $key='" . $value ."' WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) . "'");
        $db->close();
    }

    /**
     * @return void
     */
    public function resetSettings(): void {
        $db = self::getDataBase();
        Query::query("UPDATE settings SET allowTp='true' WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) . "'");
        Query::query("UPDATE settings SET allowMsg='true' WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) . "'");
        Query::query("UPDATE settings SET clearLagMessage='true' WHERE name='" . self::getDataBase()->real_escape_string($this->player->getName()) . "'");
        $db->close();
    }
}