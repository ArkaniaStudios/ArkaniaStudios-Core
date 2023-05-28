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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\data\SettingsNameIds;
use arkania\manager\RanksManager;
use arkania\manager\SettingsManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

final class MsgCommand extends BaseCommand {

    /** @var array */
    public static array $lastMessager = [];

    public function __construct() {
        parent::__construct('msg',
            'Envoie un message privé à un joueur.',
            '/msg <player> <message>',
            ['tell', 'w', 'm', 'wish']
        );
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole';


        if (count($args) < 2)
            return throw new InvalidCommandSyntaxException();

        $message = [];
        for($i = 1;$i < count($args);$i++)
            $message[] = $args[$i];
        $message = implode(' ', $message);

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);

        if (!$target instanceof Player){
            $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            return true;
        }

        $settings = new SettingsManager($target);

        if ($settings->getSettings(SettingsNameIds::MESSAGE) === true){
            if (!$player->hasPermission('arkania:permission.settings.bypass'))
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur a désactivé les messages privés.");
            else{
                $target->sendMessage("[§eMessage§f] §6" . $rank . " §7-> §6Vous §7§l» §r" . $message);
                $player->sendMessage("[§eMessage§f] §6Vous §7-> " . RanksManager::getRanksFormatPlayer($target) . " §7§l» §r" . $message);
                self::$lastMessager[$player->getName()] = $target->getName();
                self::$lastMessager[$target->getName()] = $player->getName();
            }
            return true;
        }

        $target->sendMessage("[§eMessage§f] §6" . $rank . " §7-> §6Vous §7§l» §r" . $message);
        $player->sendMessage("[§eMessage§f] §6Vous §7-> " . RanksManager::getRanksFormatPlayer($target) . " §7§l» §r" . $message);

        self::$lastMessager[$player->getName()] = $target->getName();
        self::$lastMessager[$target->getName()] = $player->getName();

        $this->sendStaffLogs(RanksManager::getRanksFormatPlayer($target) . ' -> ' . $rank . ": " . $message);

        return true;
    }
}