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

namespace arkania\utils;

use arkania\Core;
use arkania\data\DataBaseConnector;
use arkania\libs\discord\Embed;
use arkania\libs\discord\Message;
use arkania\libs\discord\Webhook;
use mysqli;
use pocketmine\Server;

final class Utils {

    /**
     * @return string
     */
    public static function getPrefix(): string {
        return Core::getInstance()->config->get('prefix');
    }

    /**
     * @param string $value
     * @return bool|int
     */
    public static function isValidArgument(string $value): bool|int {
        return preg_match('/[A-Za-z0-9]$/', $value);
    }

    /**
     * @return string
     */
    public static function getServerName(): string {
        $port = Server::getInstance()->getPort();
        if ($port === 10286)
            return 'Arkania-V2';
        elseif($port === 10297)
            return 'Theta';
        elseif($port === 10298)
            return 'Zeta';
        elseif($port === 10299)
            return 'Epsilon';
        return 'unknown';
    }

    /**
     * @deprecated
     * @return void
     * Never use if you don't tell Julien
     */
    public static function debug($key): void {
        $db = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
        if ($key === 'faction'){
            $db->query("DROP TABLE factions");
            $db->query("DROP TABLE players_faction");
        }elseif($key === 'ranks'){
            $db->query("DROP TABLE ranks");
            $db->query("DROP TABLE players_ranks");
        }elseif($key === 'money')
            $db->query("DROP TABLE money");
        elseif($key === 'inventory'){
            $db->query("DROP TABLE inventory");
            $db->query("DROP TABLE enderchest");
        }elseif($key === 'settings')
            $db->query("DROP TABLE settings");
        elseif($key === 'stats'){
            $db->query("DROP TABLE kills");
            $db->query("DROP TABLE deaths");
            $db->query("DROP TABLE inscription");
            $db->query("DROP TABLE player_time");
        }elseif($key === 'all'){
            $db->query("DROP TABLE kills");
            $db->query("DROP TABLE deaths");
            $db->query("DROP TABLE inscription");
            $db->query("DROP TABLE player_time");
            $db->query("DROP TABLE settings");
            $db->query("DROP TABLE inventory");
            $db->query("DROP TABLE enderchest");
            $db->query("DROP TABLE money");
            $db->query("DROP TABLE ranks");
            $db->query("DROP TABLE players_ranks");
            $db->query("DROP TABLE factions");
            $db->query("DROP TABLE players_faction");
        }
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $footer
     * @param int $color
     * @param string $url
     * @return void
     */
    public static function sendDiscordWebhook(string $title, string $description, string $footer, int $color, string $url): void {
        $message = new Message();
        $webhook = new Webhook($url);
        $embed = new Embed();
        $embed->setTitle($title);
        $embed->setDescription($description);
        $embed->setColor($color);
        $embed->setFooter($footer);
        $message->addEmbed($embed);
        $webhook->send($message);
    }
}