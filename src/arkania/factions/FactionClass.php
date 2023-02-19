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
use arkania\manager\FactionManager;
use arkania\utils\Query;
use arkania\utils\Utils;
use mysqli;
use pocketmine\player\Player;
use pocketmine\Server;

class FactionClass {

    /** @var Claim[][] */
    public static array $claim = [];

    public function __construct(private string $factionName, private string $ownerName, private bool $logs , private string $creationTime, private ?string $description = '', private string $url = '') {
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
        $db->query("CREATE TABLE IF NOT EXISTS factions(name VARCHAR(10), description TEXT, creation_date TEXT, ownerName VARCHAR(20), allies TEXT, members TEXT, power INT, money INT, logs BOOL, url TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS players_faction(name VARCHAR(20), faction VARCHAR(10), faction_rank VARCHAR(10))");
        $db->query("CREATE TABLE IF NOT EXISTS claim(name VARCHAR(10), x1 FLOAR, z1 FLOAR, x2 FLOAR, z2 FLOAR, world TEXT)");
        $db->close();
    }

    /**
     * @return bool
     */
    public function existFaction(): bool {
        $db = self::getDataBase()->query("SELECT * FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $faction = $db->num_rows > 0;
        if (!$faction)
            return false;
        return true;
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
                     claims,
                     power,
                     money,
                     logs,
                     url
                     ) VALUES (
                               '" . self::getDataBase()->real_escape_string($this->factionName) . "',
                                '" . self::getDataBase()->real_escape_string($this->description) ."',
                                 '" . self::getDataBase()->real_escape_string($this->creationTime) . "',
                                  '" . self::getDataBase()->real_escape_string($this->ownerName) . "',
                                  '" . serialize([]) . "',
                                  '" . serialize([]) . "',
                                  '" . serialize([]) . "',
                                  0,
                                  0,
                                  '" . $this->logs . "',
                                  '" . self::getDataBase()->real_escape_string($this->url) . "')");
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
     * @return string|bool
     */
    public function getDescription(): string|bool {
        $db = self::getDataBase()->query("SELECT description FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $description = $db->fetch_array()[0] ?? false;
        $db->close();
        return (string)$description;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string {
        $db = self::getDataBase()->query("SELECT creation_date FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $creation = $db->fetch_array()[0] ?? false;
        $db->close();
        return (string)$creation;
    }

    /**
     * @return string
     */
    public function getOwner(): string {
        $db = self::getDataBase()->query("SELECT ownerName FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $owner = $db->fetch_array()[0] ?? false;
        $db->close();
        return (string)$owner;
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
     * @param string $factions
     * @return void
     */
    public function addAllies(string $factions): void {
        $db = self::getDataBase()->query("SELECT allies FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $result = $db->fetch_array()[0];
        $result = unserialize($result);
        $result[] = $factions;
        $allies = serialize($result);
        Query::query("UPDATE factions SET allies='$allies' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param string $faction
     * @return void
     */
    public function delAllies(string $faction): void {
        $db = self::getDataBase()->query("SELECT allies FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $result = $db->fetch_array()[0];
        $result = unserialize($result);
        unset($result[array_search($faction, $result)]);
        $allies = serialize($result);
        Query::query("UPDATE factions SET allies='$allies' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
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
     * @param Player $player
     * @return void
     */
    public function addMember(Player $player): void {
        $db = self::getDataBase()->query("SELECT members FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $result = $db->fetch_array()[0];
        $result = unserialize($result);
        $result[] = $player->getName();
        $members = serialize($result);
        Query::query("UPDATE factions SET members='$members' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        Query::query("INSERT INTO players_faction(name, faction, faction_rank) VALUES ('" . self::getDataBase()->real_escape_string($player->getName()) . "', '" . $this->factionName . "', 'member')");
        $db->close();
    }

    /**
     * @param string $player
     * @return void
     */
    public function removeMember(string $player): void {
        $db = self::getDataBase()->query("SELECT members FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $result = $db->fetch_array()[0];
        $result = unserialize($result);
        unset($result[array_search($player, $result)]);
        $members = serialize($result);
        Query::query("UPDATE factions SET members='$members' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        Query::query("DELETE FROM players_faction WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $db->close();
    }

    /**
     * @param string $player
     * @return void
     */
    public function promoteMember(string $player): void {
        $db = self::getDataBase();
        Query::query("UPDATE players_faction SET faction_rank='officer' WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $db->close();
    }

    /**
     * @param string $player
     * @return void
     */
    public function demoteMember(string $player): void {
        $db = self::getDataBase();
        Query::query("UPDATE players_faction SET faction_rank='member' WHERE name='" . self::getDataBase()->real_escape_string($player) . "'");
        $db->close();
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

    /**
     * @return float|int|string
     */
    public function getMoney(): float|int|string {
        $db = self::getDataBase()->query("SELECT money FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $money = $db->fetch_array()[0] ?? 'Error';
        $db->close();
        return $money;
    }

    /**
     * @param int $money
     * @return void
     */
    public function addMoney(int $money): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET money=money + '$money' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param int $money
     * @return void
     */
    public function delMoney(int $money): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET money=money - '$money' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @return int
     */
    public function getPower(): int {
        $db = self::getDataBase()->query("SELECT power FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $power = $db->fetch_array()[0] ?? false;
        $db->close();
        return (int)$power;
    }

    /**
     * @param int $power
     * @return void
     */
    public function setPower(int $power): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET power='$power' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param int $power
     * @return void
     */
    public function addPower(int $power): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET power=power + '$power' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param int $power
     * @return void
     */
    public function delPower(int $power): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET power=power - '$power' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @return void
     */
    public function resetPower(): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET power=0 WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param string $message
     * @param string $playerName
     * @return void
     */
    public function sendFactionMessage(string $message, string $playerName): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $factionManager = new FactionManager();
            if ($factionManager->getFaction($playerName) === $this->factionName){
                $playerRank = $factionManager->getFactionRank($playerName);
                if ($playerRank === 'owner')
                    $ranks = '§cChef §f- §c';
                elseif($playerRank === 'officer')
                    $ranks = '§6Officier §f- §6';
                elseif($playerRank === 'member')
                    $ranks = '§7Membre §f- §7';
                else
                    $ranks = '§7Membre §f- §7';
                $player->sendMessage("[§eFaction§f-§eChat§f] $ranks" . $playerName . " §f» §e" . $message);
            }
        }
    }

    /**
     * @return bool
     */
    public function getLogsStatus(): bool {
        $db = self::getDataBase()->query("SELECT logs FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $logs = $db->fetch_array()[0] ?? false;
        $db->close();

        if ($logs === 1)
            return false;
        else
            return true;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setLogsStatus(bool $value): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET logs=$value WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $db->close();
    }

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void {
        $db = self::getDataBase();
        $db->query("UPDATE factions SET url='$url' WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        $db = self::getDataBase()->query("SELECT url FROM factions WHERE name='" . self::getDataBase()->real_escape_string($this->factionName) . "'");
        $url = $db->fetch_array()[0] ?? false;
        $db->close();
        return (string)$url;
    }

    /**
     * @param string $title
     * @param string $message
     * @param string $footer
     * @return void
     */
    public function sendFactionLogs(string $title, string $message, string $footer = '・Plugin faction - ArkaniaStudios'): void {
        if ($this->getLogsStatus() === true)
            Utils::sendDiscordWebhook($title, $message, $footer, 0x3374FF, $this->getUrl());
    }

    /**
     * @return array
     */
    public function getAllFaction(): array {
        $res = self::getDatabase()->query("SELECT * FROM factions");
        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[7];
        }
        $res->close();
        return $ret;
    }
}