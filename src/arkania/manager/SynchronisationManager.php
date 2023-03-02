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

use arkania\data\DataBaseConnector;
use arkania\utils\Query;
use mysqli;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class SynchronisationManager {

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
        $db->query("CREATE TABLE IF NOT EXISTS inventory(name VARCHAR(20), inventaire TEXT, armure TEXT, enderchest TEXT, xplvl INT, xpp FLOAT)");
        $db->close();
    }

    /**
     * @param Player $player
     */
    public function registerInv(Player $player): void {
        if ($player->spawned) {
            $name = $player->getName();
            $xp_lvl = $player->getXpManager()->getXpLevel();
            $xp_progress = $player->getXpManager()->getXpProgress();
            $inv_armor = [];
            $inv = [];
            $inv_ender = [];
            foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
                $inv_armor[$slot] = $item->jsonSerialize();
            }
            foreach ($player->getInventory()->getContents() as $slot => $item) {
                $inv[$slot] = $item->jsonSerialize();
            }
            foreach ($player->getEnderInventory()->getContents() as $slot => $item) {
                $inv_ender[$slot] = $item->jsonSerialize();
            }

            $db = self::getDataBase()->query("SELECT * FROM inventory WHERE name='" . $name . "'");
            if ($db->num_rows > 0)
                Query::query("UPDATE inventory SET inventaire='" . json_encode($inv) . "', armure='" . json_encode($inv_armor) . "', enderchest='" . json_encode($inv_ender) . "', xplvl='$xp_lvl', xpp='$xp_progress' WHERE name='$name'");
            else
                Query::query("INSERT INTO inventory(name,
                      inventaire,
                      armure,
                      enderchest,
                      xplvl,
                      xpp)VALUES (
                                  '$name',
                                  '" . json_encode($inv) . "',
                                  '" . json_encode($inv_armor) . "',
                                  '" . json_encode($inv_ender) . "',
                                  '$xp_lvl',
                                  '$xp_progress'
                      )");
            $db->close();
        }
    }

    /**
     * @param Player $player
     */
    public function syncPlayer(Player $player): void
    {
        $db = self::getDataBase()->query("SELECT * FROM inventory WHERE name='" . $player->getName() . "'");
        $xp_lvl = null;
        $xp_progress = null;
        $inv_armor = null;
        $inv_db = null;
        $inv_ender = null;
        foreach ($db as $result) {
            $inv_db = $result["inventaire"];
            $inv_armor = $result["armure"];
            $inv_ender = $result["enderchest"];
            $xp_lvl = $result["xplvl"];
            $xp_progress = $result["xpp"];
            break;
        }
        if ($inv_db !== null) {
            $inv = $player->getInventory();
            if ($inv !== null) {
                $inv->clearAll();
                foreach (json_decode($inv_db, true) as $slot => $item) {
                    $inv->setItem($slot, Item::jsonDeserialize($item));
                }
            }
        }
        if ($inv_armor !== null) {
            $armor = $player->getArmorInventory();
            $armor->clearAll();
            foreach (json_decode($inv_armor, true) as $slot => $item)
                $armor->setItem($slot, Item::jsonDeserialize($item));
        }
        if ($inv_ender !== null) {
            $ender = $player->getEnderInventory();
            $ender->clearAll();
            foreach (json_decode($inv_ender, true) as $slot => $item)
                $ender->setItem($slot, Item::jsonDeserialize($item));
        }
        if ($xp_lvl !== null) {
            $player->getXpManager()->setXpLevel((int)$xp_lvl);
        }
        if ($xp_progress !== null) {
            $player->getXpManager()->setXpProgress((float)$xp_progress);
        }
    }
}