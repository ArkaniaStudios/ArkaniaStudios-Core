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

namespace arkania\commands;

use arkania\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class BaseCommand extends Command {

    public function __construct(string $name, $description = "", $usageMessage = null, array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        return true;
    }

    /**
     * @param string $message
     * @return void
     */
    protected function sendStaffLogs(string $message): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
            if (isset(Core::getInstance()->staff_logs[$onlinePlayer->getName()]))
                $onlinePlayer->sendMessage("§o§7Logs: " . $message);
        }
    }

    /**
     * @param Player $player
     * @param string $message1
     * @param string $message2
     * @return void
     */
    public static function sendToastPacket(Player $player, string $message1, string $message2): void {
        $packet = ToastRequestPacket::create($message1, $message2);
        $player->getNetworkSession()->sendDataPacket($packet);
    }
}