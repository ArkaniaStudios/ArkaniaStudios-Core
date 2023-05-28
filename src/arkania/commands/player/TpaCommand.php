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
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class TpaCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('tpa',
        'Envoie une demande de téléportation à un joueur',
        '/tpa <player>');
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $target = $this->core->getServer()->getPlayerByPrefix($args[0]);

        if (!$target instanceof Player){
            $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            return true;
        }

        $player->sendMessage(Utils::getPrefix() . "§aDemande de téléportation envoyé à §e" . $target->getName() . "§a.");
        $this->core->getTeleportManager()->sendTpaRequest($target, $player);
        return true;
    }
}