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

namespace arkania\jobs\class;

use arkania\jobs\Jobs;
use arkania\utils\Query;
use arkania\utils\trait\Provider;
use pocketmine\player\Player;

class Mineur implements Jobs {
    use Provider;

    /** @var array */
    private array $jobs;

    /**
     * @return void
     */
    public static function init(): void {
        $db = (new Mineur)->getProvider();
        $db->query("CREATE TABLE IF NOT EXISTS mineur(name VARCHAR(20), xp INT, level INT, rec1 VARCHAR(12), rec2 VARCHAR(12), rec3 VARCHAR(12), rec4 VARCHAR(12), rec5 VARCHAR(12), rec6 VARCHAR(12), rec7 VARCHAR(12), rec8 VARCHAR(12), rec9 VARCHAR(12), rec10 VARCHAR(12), rec11 VARCHAR(12), rec12 VARCHAR(12), rec13 VARCHAR(12), rec14 VARCHAR(12), rec15 VARCHAR(12), rec16 VARCHAR(12), rec17 VARCHAR(12), rec18 VARCHAR(12), rec19 VARCHAR(12), rec20 VARCHAR(12), rec21 VARCHAR(12), rec22 VARCHAR(12), rec23 VARCHAR(12), rec24 VARCHAR(12), rec25 VARCHAR(12), rec26 VARCHAR(12), rec27 VARCHAR(12), rec28 VARCHAR(12), rec29 VARCHAR(12), rec30 VARCHAR(12), rec31 VARCHAR(12), rec32 VARCHAR(12), rec33 VARCHAR(12), rec34 VARCHAR(12), rec35 VARCHAR(12), rec36 VARCHAR(12), rec37 VARCHAR(12), rec38 VARCHAR(12), rec39 VARCHAR(12), rec40 VARCHAR(12), rec41 VARCHAR(12), rec42 VARCHAR(12), rec43 VARCHAR(12), rec44 VARCHAR(12), rec45 VARCHAR(12), rec46 VARCHAR(12), rec47 VARCHAR(12), rec48 VARCHAR(12), rec49 VARCHAR(12), rec50 VARCHAR(12))");
        $db->query("CREATE TABLE IF NOT EXISTS mineur_xp(name VARCHAR(20), stone INT, cold INT, iron INT gold INT, redstone INT, diamant INT, etain INT, tungsten INT)");
        $db->close();
    }

    /**
     * @return string
     */
    public function getJobName(): string {
        return 'Mineur';
    }

    /**
     * @return array
     */
    public function getMaxXp(): array {
        return [100, 500, 750, 1000, 1500, 1750, 2000, 2500, 2750, 3000, 4000, 5000, 7500, 10000, 12500, 15000, 17500, 20000, 25000, 30000, 35000, 40000, 45000, 50000, 65000, 75000, 90000, 100000, 125000, 140000, 150000, 175000, 200000, 225000, 250000, 275000, 300000, 325000, 400000, 425000, 450000, 475000, 500000, 550000, 600000, 650000, 700000, 850000, 1000000];
    }

    /**
     * @param $playerName
     * @return void
     */
    public function createJobsProfile($playerName): void {
        Query::query("INSERT INTO mineur (name,
                    xp,
                    level,
                    rec1,
                    rec2,
                    rec3,
                    rec4,
                    rec5,
                    rec6,
                    rec7,
                    rec8,
                    rec9,
                    rec10,
                    rec11, 
                    rec12,
                    rec13,
                    rec14,
                    rec15, 
                    rec16, 
                    rec17, 
                    rec18,
                    rec19,
                    rec20, 
                    rec21, 
                    rec22, 
                    rec23,
                    rec24,
                    rec25,
                    rec26,
                    rec27,
                    rec28,
                    rec29,
                    rec30,
                    rec31,
                    rec32, 
                    rec33,
                    rec34, 
                    rec35, 
                    rec36, 
                    rec37, 
                    rec38, 
                    rec39,
                    rec40, 
                    rec41, 
                    rec42,
                    rec43,
                    rec44, 
                    rec45, 
                    rec46, 
                    rec47,
                    rec48,
                    rec49,
                    rec50)VALUES(
                                 '" . $playerName . "',
                                 0,
                                 0,
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible',
                                 'indisponible'" .
            "
                                 
                    )");
        Query::query("INSERT INTO mineur_xp(name, stone, cold, iron, gold, redstone, dimant, etain, tungsten) VALUES ('" . $playerName . "', 1, 3, 4, 0, 3, 0, 0, 0)");
    }

    /**
     * @param $playerName
     * @param int $value
     * @return void
     */
    public function addXp($playerName, int $value): void {
        $xp = $this->getPlayerXp($playerName);
        $this->jobs[$playerName]['xp'] = $xp + $value;
        if ($this->getPlayerXp($playerName) >= $this->getMaxXp()[$this->getPlayerLevel($playerName)])
            $this->updateLevel($playerName, $this->getPlayerLevel($playerName) + 1);
    }

    /**
     * @param $playerName
     * @param int $level
     * @return void
     */
    public function updateLevel($playerName, int $level): void {
        Query::query("UPDATE mineur SET level='$level' WHERE name='$playerName'");
        Query::query("UPDATE mineur SET `rec$level`='disponible' WHERE name='$playerName'");
    }

    /**
     * @param $playerName
     * @return int
     */
    public function getPlayerXp($playerName): int {
        return (int)$this->jobs[$playerName][0];
    }

    /**
     * @param $playerName
     * @return int
     */
    public function getPlayerLevel($playerName): int {
        return (int)$this->jobs[$playerName][1];
    }

    /**
     * @param $playerName
     * @return void
     */
    public function resetPlayerJob($playerName): void {
        Query::query("DELETE FROM mineur WHERE name='$playerName'");
        $this->createJobsProfile($playerName);
    }

    /**
     * @param $playerName
     * @param int $level
     * @return void
     */
    public function sendReward($playerName, int $level): void {
    }

    /**
     * @param $playerName
     * @param $value
     * @return bool
     */
    public function canRecupReward($playerName, $value): bool {
        $db = $this->getProvider()->query("SELECT $value FROM mineur WHERE name='$playerName'");
        $result = $db->fetch_array()[0] ?? false;
        $db->close();
        if ($result === 'indisponible')
            return false;
        if ($result === 'disponible')
            return true;
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function synchroJobsOnJoin(Player $player): void {
        $xpDB = $this->getProvider()->query("SELECT xp FROM mineur WHERE name='" . $player->getName() . "'");
        $xp = $xpDB->fetch_array()[0] ?? 0;
        $levelDB = $this->getProvider()->query("SELECT level FROM mineur WHERE name='" . $player->getName() . "'");
        $level = $levelDB->fetch_array()[0] ?? 0;
        $this->jobs[$player->getName()] = array($xp, $level);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function synchroJobsOnQuit(Player $player): void {
        $xp = $this->getPlayerXp($player->getName());
        $level = $this->getPlayerLevel($player->getName());
        Query::query("UPDATE mineur SET xp='$xp', WHERE name='" . $player->getName() . "'");
        Query::query("UPDATE mineur SET level='$level', WHERE name='" . $player->getName() . "'");
    }
}