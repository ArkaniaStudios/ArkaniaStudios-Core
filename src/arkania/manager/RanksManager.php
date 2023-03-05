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

use arkania\Core;
use arkania\exception\PermissionMissingException;
use arkania\utils\Query;
use arkania\utils\trait\Provider;
use mysqli;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\Server;

final class RanksManager {
    use Provider;

    /** @var array  */
    public array $attachment = [];

    /* DataBase */
    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi {
        return (new RanksManager)->getProvider();
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

    /**
     * @param $playerName
     * @return bool
     */
    public function existPlayer($playerName): bool {
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
        return !$ranks ? 'Joueur' : $ranks;
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
        $target = Server::getInstance()->getPlayerExact($players->fetch_array()[0]);
        if ($target instanceof Player){
            $this->updatePermission($target);
        }
    }

    /* Permission */

    /**
     * @param string $playerName
     * @return array
     */
    public function getPermission(string $playerName): array {
        $rank = $this->getPlayerRank($playerName);
        $db = self::getDataBase()->query("SELECT permissions FROM ranks WHERE name='" . $rank . "'");
        $db2 = self::getDataBase()->query("SELECT permissions FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
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
     * @param IPlayer $player
     * @return void
     */
    public function updatePermission(IPlayer $player): void{

        if ($player instanceof Player) {
            foreach ($this->getPermission($player->getName()) as $permission) {
                $attachment = $this->getAttachment($player);
                $attachment->clearPermissions();
                $attachment->setPermission($permission, true);
            }
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
            $this->updatePermission($player);
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
            Query::query("INSERT INTO players_ranks(name, ranks, permissions) VALUES ('" . self::getDataBase()->real_escape_string($playerName) . "', '$rank', '" . serialize([]) . "')");
        $target = Server::getInstance()->getPlayerExact($playerName);

        if ($target instanceof Player){
            if (isset($this->attachment[$target->getName()]))
                $this->updatePermission($target);
            $this->updateNameTag($target);
        }
        $db->close();
    }

    /**
     * @param string $playerName
     * @return void
     */
    public function setDefaultRank(string $playerName): void {
        $db = self::getDataBase()->query("SELECT * FROM players_ranks WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        Query::query("INSERT INTO players_ranks(name, ranks, permissions) VALUES ('" . $playerName . "', 'Joueur', '" . serialize([]) . "')");
        $target = Server::getInstance()->getPlayerExact($playerName);
        $db->close();
        if ($target instanceof Player)
            $this->updateNameTag($target);
    }

    /* Synchronisation */
    /**
     * @param Player $player
     * @return void
     */
    public function synchroJoinRank(Player $player): void {
        $name = $player->getName();
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
        $name = $player->getName();
        $rank = $this->getPlayerRank($player->getName());
        $db = self::getDatabase();
        Query::query("UPDATE players_ranks SET ranks = '$rank' WHERE name='" . self::getDatabase()->real_escape_string($name) . "'");
        $db->close();
    }

    /* Format */

    /**
     * @param Player $player
     * @param string $message
     * @param string|null $nickName
     * @return string
     */
    public function getChatFormat(Player $player, string $message, string $nickName = null): string {
        $ranks = $this->getPlayerRank($player->getName());
        $db = self::getDataBase()->query("SELECT format FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($ranks) . "'");
        $format = $db->fetch_array()[0] ?? false;
        $db->close();
        $faction = new FactionManager();
        if (is_null($nickName))
            $name = $player->getName();
        else
            $name = $nickName;
        return str_replace(['{FACTION_RANK}','{FACTION}', '{PLAYER}', '{MESSAGE}'], [$this->getFactionRankFormat($player), $faction->getFaction($player->getName()), $name, $message], $format);
    }

    /**
     * @param Player $player
     * @param string|null $nickName
     * @return void
     */
    public function updateNameTag(Player $player, string $nickName = null): void {
        $ranks = $this->getPlayerRank($player->getName());
        $db = self::getDataBase()->query("SELECT nametag FROM ranks WHERE name='" . self::getDataBase()->real_escape_string($ranks) . "'");
        $format = $db->fetch_array()[0] ?? false;
        $db->close();
        $faction = new FactionManager();
        if (is_null($nickName))
            $name = $player->getName();
        else
            $name = $nickName;
        $nametag = str_replace(['{FACTION_RANK}', '{FACTION}', '{LINE}', '{PLAYER}'], [$this->getFactionRankFormat($player), $faction->getFaction($player->getName()), "\n", $name], $format);
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
        if (!in_array($permission, $permissionArray)) return;
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
        return $this->ex($ranks);
    }

    public function getRankColorToString(string $ranks): string {
        return $this->ex($ranks);
    }

    /**
     * @param string $playerName
     * @return string
     */
    private function getRankColorBis(string $playerName): string {
        $ranks = $this->getPlayerRank($playerName);
        if ($ranks === 'Joueur') return '§7';
        if ($ranks === 'Booster') return '§d';
        if ($ranks === 'Noble') return '§e';
        if ($ranks === 'Héro') return '§6';
        if ($ranks === 'Seigneur') return '§4';
        if ($ranks === 'Vidéaste') return '§c';
        if ($ranks === 'Helper') return '§a';
        if ($ranks === 'Modérateur') return '§3';
        if ($ranks === 'Opérateur') return '§1';
        if ($ranks === 'Développeur') return '§2';
        if ($ranks === 'Administrateur') return '§6';
        if ($ranks === 'Développeurplus') return '§2';
        if ($ranks === 'Co-Fondateur') return '§c';
        if ($ranks === 'Fondateur') return '§4';
        return '§7';
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getFactionRankFormat(Player $player) : string {
        $factionManager = new FactionManager();
        $fac_rank = $factionManager->getFactionRank($player->getName());
        if ($fac_rank === 'owner')
            return '**';
        elseif($fac_rank === 'officer')
            return '*';
        return '';
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getRanksFormatPlayer(Player $player): string{
        return (new RanksManager)->getRankColor($player->getName()) . ' §f- ' . (new RanksManager)->getRankColorBis($player->getName()) . $player->getName() . ' §r';
    }

    /**
     * @param string $ranks
     * @return string
     */
    private function ex(string $ranks): string
    {
        if ($ranks === 'Joueur') return '§7Joueur';
        if ($ranks === 'Booster') return '§dBooster';
        if ($ranks === 'Noble') return '§eNoble';
        if ($ranks === 'Héro') return '§6Héro';
        if ($ranks === 'Seigneur') return '§4Seigneur';
        if ($ranks === 'Vidéaste') return '§cVidéaste';
        if ($ranks === 'Helper') return '§aHelper';
        if ($ranks === 'Modérateur') return '§3Modérateur';
        if ($ranks === 'Opérateur') return '§1Opérateur';
        if ($ranks === 'Développeur') return '§2Développeur';
        if ($ranks === 'Administrateur') return '§6Administrateur';
        if ($ranks === 'Développeurplus') return '§2Développeur';
        if ($ranks === 'Co-Fondateur') return '§cCo§f-§cFondateur';
        if ($ranks === 'Fondateur') return '§4Fondateur';
        return '§7Joueur';
    }

    /** @var array */
    public static array $rankList = [
        'Fondateur',
        'Co-Fondateur',
        'Développeurplus',
        'Administrateur',
        'Opérateur',
        'Modérateur',
        'Développeur',
        'Helper',
        'Vidéaste',
        'Seigneur',
        'Héro',
        'Noble',
        'Booster',
        'Joueur'
    ];

    /**
     * @param string $playerName
     * @param string $targetName
     * @return bool
     */
    public static function compareRank(string $playerName, string $targetName): bool {
        $rankp = (new RanksManager)->getPlayerRank($playerName);
        $rankt = (new RanksManager)->getPlayerRank($targetName);
        return self::$rankList[array_search($rankp, self::$rankList)] < self::$rankList[array_search($rankt, self::$rankList)];
    }
    public function classPlayersByRank(): array{
        $array = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if ($player->isOnline())
                $array[$this->getRankColor($player->getName())][] = $player;
        }
        return $array;
    }

    /**
     * @return array|string[]
     */
    public function getRanksList(): array {
        return self::$rankList;
    }

}