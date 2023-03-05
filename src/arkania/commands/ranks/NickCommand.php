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
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class NickCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('nick',
        'Nick - ArkaniaStudios',
        '/nick <name>');
        $this->setPermission('arkania:permission.nick');
        $this->core = $core;
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (!Utils::isValidArgument($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe nom n'est pas valide. Merci de ne par mettre d'espace ou de caractères spéciaux.");
        }

        if ((strlen($args[0]) < 3 || strlen($args[0]) > 16)){
            $player->sendMessage(Utils::getPrefix() . "§cVotre pseudo doit contenir au moins §e3 caractères §cet au maximum §e16§c.");
            return true;
        }

        if (strtolower($args[0]) === 'reset') {
            $this->core->getNickManager()->removePlayerNick($player);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez remis votre pseudo par défaut.");
        }else{
            $this->core->getNickManager()->setPlayerNick($player, $args[0]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez changer votre pseudo en §e" . $args[0] . "§a.");
        }
        return true;
    }
}