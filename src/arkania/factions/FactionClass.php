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

namespace arkania\factions;

use arkania\data\DataBaseConnector;
use arkania\utils\Query;
use mysqli;

class FactionClass {

    public function __construct(private string $factionName, private string $ownerName, private string $creationTime, private ?string $description = '') {
    }

    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi{
        return new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
    }

    /**
     * @return void
     */
    public static function init(): void {
        $db = self::getDataBase();
        $db->query("CREATE TABLE IF NOT EXISTS factions(name VARCHAR(10), description TEXT, creation_date TEXT, ownerName VARCHAR(20), allies TEXT, members TEXT, claims TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS players_faction(name VARCHAR(20), faction VARCHAR(10), faction_rank VARCHAR(10))");
        $db->close();
    }

    /**
     * @return bool
     */
    public function existFaction(): bool {
        $db = self::getDataBase()->query("SELECT * FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $faction = $db->num_rows > 0;
        if (!$faction)
            return true;
        return false;
    }


    /**
     * @return void
     */
    public function createFaction(): void {
        $db = self::getDataBase();
        Query::query("INSERT INTO factions(name,
                     description,
                     creation_date,
                     ownerName,
                     allies,
                     members,
                     claims
                     ) VALUES (
                               '" . self::getDataBase()->real_escape_string($this->factionName) . "',
                                '" . self::getDataBase()->real_escape_string($this->description) ."',
                                 '" . self::getDataBase()->real_escape_string($this->creationTime) . "',
                                  '" . self::getDataBase()->real_escape_string($this->ownerName) . "',
                                  '" . serialize([]) . "',
                                  '" . serialize([]) . "',
                                  '" . serialize([]) . "')");
        Query::query("INSERT INTO players_faction(name,
                            faction,
                            faction_rank
                            ) VALUES (
                                      '" . self::getDataBase()->real_escape_string($this->ownerName) . "',
                                      '" . self::getDataBase()->real_escape_string($this->factionName) . "',
                                      '" . self::getDataBase()->real_escape_string(FactionIds::OWNER) . "')");
        $db->close();
    }

    /**
     * @return void
     */
    public function disbandFaction(): void {
        $db = self::getDataBase();
        Query::query("DELETE FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        Query::query("DELETE FROM players_faction WHERE faction='" . self::getDataBase()->real_escape_string($this->factionName) ."'");
        $db->close();
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        $db = self::getDataBase()->query("SELECT description FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $description = $db->fetch_array()[0] ?? false;
        $db->close();
        return $description;
    }

    /**
     * @return string|bool
     */
    public function getCreationDate(): string|bool {
        $db = self::getDataBase()->query("SELECT creation_date FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $creation = $db->fetch_array()[0] ?? false;
        $db->close();
        return $creation;
    }

    /**
     * @return string|bool
     */
    public function getOwner(): string|bool {
        $db = self::getDataBase()->query("SELECT ownerName FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $owner = $db->fetch_array()[0] ?? false;
        $db->close();
        return $owner;
    }

    /**
     * @return array|bool
     */
    public function getAllies(): array|bool {
        $db = self::getDataBase()->query("SELECT allies FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $allies = $db->fetch_array()[0] ?? false;
        $db->close();
        return unserialize($allies);
    }

    /**
     * @return array|bool
     */
    public function getMembers(): array|bool {
        $db = self::getDataBase()->query("SELECT members FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $members = $db->fetch_array()[0] ?? false;
        $db->close();
        return unserialize($members);
    }

    /**
     * @return array|bool
     */
    public function getClaim(): array|bool {
        $db = self::getDataBase()->query("SELECT claims FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $claims = $db->fetch_array()[0] ?? false;
        $db->close();
        return unserialize($claims);
    }
}