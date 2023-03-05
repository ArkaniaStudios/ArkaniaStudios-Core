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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\events\players\PlayerDeathEvent;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class BackCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('back',
        'Back - ArkaniaStudios',
        '/back');
        $this->setPermission('arkania:permission.back');
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        if (!isset(PlayerDeathEvent::$backPosition[$player->getName()])){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la lieu de mort récent.");
            return true;
        }

        $player->teleport(PlayerDeathEvent::$backPosition[$player->getName()]);
        $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté à votre dernier lieu de mort.");
        unset(PlayerDeathEvent::$backPosition[$player->getName()]);
        return true;
    }
}