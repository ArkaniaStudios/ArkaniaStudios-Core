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

final class SynchronisationManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

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
        $db->query("CREATE TABLE IF NOT EXISTS inventory(name VARCHAR(20), inventaire TEXT, armure TEXT, experience INT)");
        $db->query("CREATE TABLE IF NOT EXISTS enderchest(name VARCHAR(20), inventaire TEXT)");
        $db->close();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function register(Player $player): void {
        $db = self::getDataBase();
        $inventory = $player->getInventory()->getContents();
        $armor = $player->getArmorInventory()->getContents();

        $dataInventory = [];
        $dataArmor = [];

        foreach ($inventory as $slot => $item)
            $dataInventory[$slot] = $item;
        foreach ($armor as $slot => $item)
            $dataArmor[$slot] = $item;

        $dataInventory = base64_encode(serialize($dataInventory));
        $dataArmor = base64_encode(serialize($dataArmor));
        $experience = $player->getXpManager()->getXpLevel();
        $db->query("INSERT INTO inventory(name, inventaire, armure, experience) VALUES ('" . self::getDataBase()->real_escape_string($player->getName()) . "', '$dataInventory', '$dataArmor', '$experience')");
        $db->close();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isRegistered(Player $player): bool {
        $db = self::getDataBase();
        $result = $db->query("SELECT * FROM inventory WHERE name='" . self::getDataBase()->real_escape_string($player->getName()) . "'");
        $db->close();
        if ($result->num_rows <= 0)
            return false;
        else
            return true;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getArmorInventory(string $name): array{
        $db = self::getDataBase();

        $result = $db->query("SELECT * FROM inventory WHERE name='" . self::getDataBase()->real_escape_string($name) . "'");
        $db->close();

        return unserialize(base64_decode($result->fetch_array()['armure']));
    }

    /**
     * @param string $name
     * @return array
     */
    public function getInventory(string $name): array{
        $db = self::getDataBase();

        $result = $db->query("SELECT * FROM inventory WHERE name='" . self::getDataBase()->real_escape_string($name) . "'");
        $db->close();

        return unserialize(base64_decode($result->fetch_array()['inventaire']));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getExperience(string $name): mixed
    {
        $db = self::getDataBase();

        $result = $db->query("SELECT * FROM inventory WHERE name='" . self::getDataBase()->real_escape_string($name) . "'");
        $db->close();

        return $result->fetch_array()['experience'];
    }

    public function saveInventory(Player $player): void {
        $db = self::getDataBase();

        $inventory = $player->getInventory()->getContents();
        $armor = $player->getArmorInventory()->getContents();

        $dataInventory = [];
        $dataArmor = [];

        foreach ($inventory as $slot => $item)
            $dataInventory[$slot] = $item;
        foreach ($armor as $slot => $item) {
            switch ($slot) {
                case 0:
                    $dataArmor['helmet'] = $item;
                    break;
                case 1:
                    $dataArmor['chestplate'] = $item;
                    break;
                case 2:
                    $dataArmor['leggings'] = $item;
                    break;
                case 3:
                    $dataArmor['boots'] = $item;
                    break;
            }
        }

        $dataInventory = base64_encode(serialize($dataInventory));
        $dataArmor = base64_encode(serialize($dataArmor));
        $experience = $player->getXpManager()->getXpLevel();

        Query::query("UPDATE inventory SET inventaire='$dataInventory', armure='$dataArmor', experience='$experience' WHERE name='" . self::getDataBase()->real_escape_string($player->getName()) . "'");
        $db->close();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function restorInventory(Player $player): void {
        $name = $player->getName();
        $inventaire = $this->getInventory($name);
        $armure = $this->getArmorInventory($name);

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        foreach ($inventaire as $slot => $item)
            $player->getInventory()->setItem($slot, $item);

        if (isset($armure['helmet'])) $player->getArmorInventory()->setHelmet($armure['helmet']);
        if (isset($armure['chestplate'])) $player->getArmorInventory()->setChestplate($armure['chestplate']);
        if (isset($armure['leggings'])) $player->getArmorInventory()->setLeggings($armure['leggings']);
        if (isset($armure['boots'])) $player->getArmorInventory()->setBoots($armure['boots']);

        $player->getXpManager()->setXpLevel((int)$this->getExperience($name));

    }

}