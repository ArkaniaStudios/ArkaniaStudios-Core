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

use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use arkania\commands\BaseCommand;

final class BoutiqueCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('boutique',
        'Donne le liens de la boutique d\'arkania',
        '/boutique');
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        $player->sendMessage(Utils::getPrefix() . "Voici le liens vers notre boutique: §ehttps://arkaniastudios.com/boutique§f.");
        return true;
    }

}