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
use arkania\factions\FactionClass;
use arkania\utils\trait\Provider;
use mysqli;

final class FactionManager {
    use Provider;

    /**
     * @return mysqli
     */
    private static function getDataBase(): MySQLi{
        return (new FactionManager)->getProvider();
    }

    /**
     * @param string $factionName
     * @param string $playerName
     * @param bool $logs
     * @param string $creationDate
     * @param string $description
     * @param string $url
     * @return FactionClass
     */
    public function getFactionClass(string $factionName, string $playerName,bool $logs = true, string $creationDate = '', string $description = '', string $url = ''): FactionClass {
        return new FactionClass($factionName, $playerName, $logs , $creationDate, $description, $url);
    }


    /**
     * @param string $playerName
     * @return string|bool
     */
    public function getFaction(string $playerName): string|bool {
        $db = self::getDataBase()->query("SELECT faction FROM players_faction WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $faction = $db->fetch_array()[0] ?? '...';
        $db->close();
        return $faction;
    }

    /**
     * @param string $playerName
     * @return string
     */
    public function getFactionRank(string $playerName): string {
        $db = self::getDataBase()->query("SELECT faction_rank FROM players_faction WHERE name='" . self::getDataBase()->real_escape_string($playerName) . "'");
        $factionRank = $db->fetch_array()[0] ?? '';
        $db->close();
        return $factionRank;
    }

    /**
     * @deprecated
     * @return void
     * Never use if you don't tell Julien
     */
    public static function __debug__($key): void{
        $db = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);

        if ($key === 'faction') {
            $db->query("DROP TABLE factions");
            $db->query("DROP TABLE players_faction");
        }
    }

    /**
     * @return void
     */
    public function loadAllConfig(): void{
        $db = $this->getProvider()->query("SELECT * FROM claims");
        $result = $db->fetch_all() ?? false;
        if ($result === false){
            return;
        }

        foreach ($result as $value)
            FactionClass::$claim[$value[1].':'.$value[2].':'.$value[3]] = [
                'x' => $value[1],
                'z' => $value[2],
                'world' => $value[3],
                'faction' => $value[0]
            ];
        $db->close();
    }
}