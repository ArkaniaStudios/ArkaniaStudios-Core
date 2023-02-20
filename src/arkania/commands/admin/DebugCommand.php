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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\data\WebhookData;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class DebugCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('debug',
        'Debug - ArkaniaStudios',
        '/debug <type>');
        $this->setPermission('arkania:permission.debug');
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if ($player->getName() !== 'Julien8436'){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas exécuter cette commande.");
            return true;
        }

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $debug_string = [
            'faction',
            'ranks',
            'money',
            'settings',
            'stats',
            'all'
        ];

        if (!in_array($args[0], $debug_string)){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas debug ce type de système.");
            return true;
        }

        Utils::__debug__($args[0]);
        $player->sendMessage(Utils::getPrefix() . "§c/!\ Vous avez debug le système de " . $args[0] . " /!\ ");
        Utils::sendDiscordWebhook('**DEBUG**', "⚠ **" . $player->getName() . "** vient de debug la database **" . $args[0] . "** ⚠", '・ArkaniaStudios - DebugSystème', 0x9C0505, WebhookData::DEBUG);
        return true;
    }

}