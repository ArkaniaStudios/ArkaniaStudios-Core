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

namespace arkania\commands\admin\ranks;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

final class AddPermissionCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('addpermission',
        'Addpermission - ArkaniaStudios',
        '/addpermission <rank> <permission>');
        $this->setPermission('arkania:permission.addpermission');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        if (!$this->core->getRanksManager()->existRank($args[0])) {
            $player->sendMessage(Utils::getPrefix() . "§cCe grade n'existe pas.");
            return true;
        }

        $this->core->getRanksManager()->addPermission($args[0], $args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez ajouté la permission §c" . $args[1] . "§f au grade §c" . $args[0] . "§f.");

        $this->sendStaffLogs($player->getName() . " vient d'ajouter la permission " . $args[1] . " au grade " . $args[0] . ".");
        return true;
    }
}