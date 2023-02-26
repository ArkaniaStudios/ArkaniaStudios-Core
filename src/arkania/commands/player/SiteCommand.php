<?php

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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

class SiteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('site',
        'Site - ArkaniaStudios',
        '/site',
        ['web']);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        $player->sendMessage(Utils::getPrefix() . "Voici le site web d'arkania : §ehttps://arkaniastudios.com/");
        return true;
    }
}