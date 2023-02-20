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
use arkania\exception\PermissionMissingException;
use arkania\utils\Query;
use mysqli;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\Server;

final class RanksManager {

    /** @var array  */
    public array $attachment = [];

    /* DataBase */
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
        $db->query("CREATE TABLE IF NOT EXISTS players_ranks(name TEXT, ranks TEXT, permissions TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS ranks(name TEXT, format TEXT, nametag TEXT, permissions TEXT)");
        $db->close();
    }

    /* Base */

    /**
     * @param string $rankName
     * @return bool
     */
    public function existRank(string $rankName): bool {
        $db = self::getDataBase()->query("SELECT * FROM ranks WHERE name='$rankName'");
        $ranks = $db->num_rows > 0;
        $db->close();
        if ($ranks)
            return true;
        return false;
    }

    public function existPlayer(string $playerName): bool {
        $db = self::getDataBase()->query("SELECT * FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) ."'");
        $player = $db->num_rows > 0;
        $db->close();
        if ($player)
            return true;
        return false;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getPlayerRank(string $playerName): string {
        $db = self::getDataBase()->query("SELECT ranks FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $ranks = $db->fetch_array()[0] ?? false;
        $db->close();
        return !$ranks ? '§7Joueur' : $ranks;
    }

    /* Management rank */
    /**
     * @param string $rankName
     * @return void
     */
    public function addRank(string $rankName): void {
        $db = self::getDataBase();
        Query::query("INSERT INTO ranks(name, format, nametag, permissions) VALUES ('" . self::getDataBase()->real_escape_string($rankName) . "', '[{FACTION}] [" . self::getDataBase()->real_escape_string($rankName) . "] {PLAYER}: {MESSAGE}', '[{FACTION}] {LINE} {PLAYER}', '" . serialize([]) ."')");
        $db->close();
    }

    /**
     * @param string $rankName
     * @return void
     */
    public function delRank(string $rankName): void {
        $db = self::getDataBase();
        Query::query("DELETE FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $db->close();

        $players = self::getDataBase()->query("SELECT name FROM player_ranks WHERE ranks='" . self::getDataBase()->real_escape_string($rankName) . "'");
        var_dump($players);
        $target = Server::getInstance()->getPlayerExact($players->fetch_array()[0]);
        var_dump($target);
        if ($target instanceof Player){
            $this->updatePermission($target);
        }
    }

    /* Permission */

    /**
     * @param Player $player
     * @return array
     */
    public function getPermission(Player $player): array {
        $rank = $this->getPlayerRank($player->getName());
        $db = self::getDataBase()->query("SELECT permissions FROM ranks WHERE name='" . $rank . "'");
        $db2 = self::getDataBase()->query("SELECT permissions FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($player->getName()) . "'");
        $permission1 = unserialize($db->fetch_array()[0]);
        $permission2 = unserialize($db2->fetch_array()[0]);
        $db->close();
        $db2->close();
        return array_merge($permission1, $permission2);
    }

    public function getAttachment(Player $player): ?PermissionAttachment {
        $UUID = $player->getUniqueId()->toString();
        if (!isset($this->attachment[$UUID]))
            return throw new PermissionMissingException('PermissionError: Permission attachment are null !');
        return $this->attachment[$UUID];
    }

    /**
     * @param Player $player
     * @return void
     */
    public function updatePermission(Player $player): void{
        $permissions = $this->getPermission($player);

        foreach ($permissions as $permission){
            $attachment = $this->getAttachment($player);
            $attachment->clearPermissions();
            $attachment->setPermissions($permission);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function register(Player $player): void {
        $UUID = $player->getUniqueId()->toString();
        if (!isset($this->attachment[$UUID])){
            $attachment = $player->addAttachment(Core::getInstance());
            $this->attachment[$UUID] = $attachment;
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function unRegister(Player $player): void {
        $UUID = $player->getUniqueId()->toString();
        if (isset($this->attachment[$UUID]))
            unset($this->attachment[$UUID]);
    }

    /**
     * @param string $playerName
     * @param string $rank
     * @return void
     */
    public function setRank(string $playerName, string $rank): void {
        $db = self::getDataBase()->query("SELECT * FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0)
            Query::query("UPDATE players_ranks SET ranks='$rank' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        else
            Query::query("INSERT INTO players_ranks(name, ranks, permissions) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '$rank', '" . serialize([]) ."')");

        $target = Server::getInstance()->getPlayerExact($playerName);

        if ($target instanceof Player){
            $this->updatePermission($target);
            $this->updateNameTag($target);
        }
        $db->close();
    }

    /* Synchronisation */
    /**
     * @param Player $player
     * @return void
     */
    public function synchroJoinRank(Player $player): void {
        $name = strtolower($player->getName());
        $data = self::getDatabase()->query("SELECT ranks FROM players_ranks WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $rank = $data->fetch_array()[0] ?? false;
        $this->setRank($player->getName(), $rank);
        $data->close();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function synchroQuitRank(Player $player): void {
        $name = strtolower($player->getName());
        $rank = $this->getPlayerRank($player->getName());
        $db = self::getDatabase();
        Query::query("UPDATE players_ranks SET ranks = '$rank' WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $db->close();
    }

    /* Format */

    /**
     * @param Player $player
     * @param string $message
     * @return string
     */
    public function getChatFormat(Player $player, string $message): string {
        $ranks = $this->getPlayerRank($player->getName());
        $db = self::getDataBase()->query("SELECT format FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($ranks) . "'");
        $format = $db->fetch_array()[0] ?? false;
        $db->close();
        $faction = new FactionManager();
        return str_replace(['{FACTION}', '{PLAYER}', '{MESSAGE}'], [$faction->getFaction($player->getName()), $player->getName(), $message], $format);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function updateNameTag(Player $player): void {
        $ranks = $this->getPlayerRank($player->getName());
        $db = self::getDataBase()->query("SELECT nametag FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($ranks) . "'");
        $format = $db->fetch_array()[0] ?? false;
        $db->close();
        $faction = new FactionManager();
        $nametag = str_replace(['{FACTION}', '{LINE}', '{PLAYER}'], [$faction->getFaction($player->getName()), "\n", $player->getName()], $format);
        $player->setNameTag($nametag);
    }

    /**
     * @param string $rankName
     * @param string $format
     * @return void
     */
    public function updateRankFormat(string $rankName, string $format): void {
        $db = self::getDataBase();
        $db->query("UPDATE ranks SET format='$format' WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $db->close();
    }

    /**
     * @param string $rankName
     * @param string $format
     * @return void
     */
    public function updateNametagFormat(string $rankName, string $format): void {
        $db = self::getDataBase();
        $db->query("UPDATE ranks SET nametag='$format' WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $db->close();

        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if ($this->getPlayerRank($player->getName()) === $rankName)
                $this->updateNameTag($player);
        }

    }

    /**
     * @param string $rankName
     * @param string $permission
     * @return void
     */
    public function addPermission(string $rankName, string $permission): void {
        $db = self::getDataBase()->query("SELECT permissions FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $permissionArray = unserialize($db->fetch_array()[0]);
        $permissionArray[] = $permission;
        Query::query("UPDATE ranks SET permissions='" . serialize($permissionArray) . "' WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $db->close();

        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer)
            $this->updatePermission($onlinePlayer);
    }

    /**
     * @param string $rankName
     * @param string $permission
     * @return void
     */
    public function delPermission(string $rankName, string $permission): void {
        $db = self::getDataBase()->query("SELECT permissions FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $permissionArray = unserialize($db->fetch_array()[0]);
        if (!in_array($permission, $permissionArray));
        unset($permissionArray[array_search($permission, $permissionArray)]);
        asort($permissionArray);
        Query::query("UPDATE ranks SET permissions='" . serialize($permissionArray) . "' WHERE name='" . self::getDataBase()->real_escape_string($rankName) . "'");
        $db->close();

        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer)
            $this->updatePermission($onlinePlayer);
    }

    /**
     * @param string $playerName
     * @param string $permission
     * @return void
     */
    public function addPlayerPermission(string $playerName, string $permission): void {
        $db = self::getDataBase()->query("SELECT * FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0){
            $db = self::getDataBase()->query("SELECT permissions FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
            $permissionArray = unserialize($db->fetch_array()[0]);
            $permissionArray[] = $permission;
            Query::query("UPDATE players_ranks SET permissions='" . serialize($permissionArray) . "' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        }else
            Query::query("INSERT INTO players_ranks(name, ranks, permissions) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '" . serialize([$permission]) . "', '" . serialize([]) ."')");
        $db->close();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer)
            $this->updatePermission($onlinePlayer);
    }

    /**
     * @param string $playerName
     * @param string $permission
     * @return void
     */
    public function delPlayerPermission(string $playerName, string $permission): void {
        $db = self::getDataBase()->query("SELECT * FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        if ($db->num_rows > 0){
            $db = self::getDataBase()->query("SELECT permissions FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
            $permissionArray = unserialize($db->fetch_array()[0]);
            if (!in_array($permission, $permissionArray));
            unset($permissionArray[array_search($permission, $permissionArray)]);
            asort($permissionArray);
            Query::query("UPDATE players_ranks SET permissions='" . serialize($permissionArray) . "' WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        }else
            Query::query("INSERT INTO players_ranks(name, ranks, permissions) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '" . serialize([$permission]) . "', '" . serialize([]) ."')");
        $db->close();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer)
            $this->updatePermission($onlinePlayer);
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getRankColor(string $playerName): string {
        $ranks = $this->getPlayerRank($playerName);
        if ($ranks === 'Joueur') return '§7Joueur';
        if ($ranks === 'Développeur') return '§2Développeur';
        if ($ranks === 'Administrateur') return '§6Administrateur';
        if ($ranks === 'Co-Fondateur') return '§cCo§f-§cFondateur';
        if ($ranks === 'Fondateur') return '§4Fondateur';
        return '§7Joueur';
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getRanksFormatPlayer(Player $player): string{
        return '';
    }

}