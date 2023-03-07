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
use arkania\manager\HomeManager;
use arkania\utils\Utils;
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class DelHomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('delhome',
        'Delhome - ArkaniaStudios',
        '/delhome <homeName>');
    }


    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     * @throws JsonException
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $homeManager = new HomeManager($player->getName());
        if (!$homeManager->existHome($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe home n'existe pas. Vous ne pouvez donc pas le supprimer.");
            return true;
        }

        $homeManager->delHome($args[0]);
        $player->sendMessage(Utils::getPrefix() . "§cVous avez supprimé le home §e" . $args[0] . "§c.");
        return true;
    }

}