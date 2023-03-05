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

namespace arkania\commands\staff;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class EnderinvseeCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('enderinvsee',
        'Enderinvsee - ArkaniaStudios',
        '/enderinvsee <player>');
        $this->setPermission('arkania:permission.enderinvsee');
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

        $target = $this->core->getServer()->getPlayerByPrefix($args[0]);

        if (!$target instanceof Player){
            $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            return true;
        }

        $this->core->getFormManager()->sendEnderInvseeForm($player, $target);
        return true;
    }
}