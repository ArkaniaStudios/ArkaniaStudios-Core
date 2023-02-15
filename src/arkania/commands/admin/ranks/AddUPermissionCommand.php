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

namespace arkania\commands\admin\ranks;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class AddUPermissionCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('addupermission',
            'Addupermission - ArkaniaStudios',
            '/addupermission <rank> <permission>');
        $this->setPermission('arkania:permission.addupermission');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        $this->core->ranksManager->addPlayerPermission($target, $args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez ajouté la permission §c" . $args[1] . "§f au joueur §c" . $target . "§f.");

        $this->sendStaffLogs($player->getName() . " vient d'ajouter la permission " . $args[1] . " au joueur " . $target . ".");
        return true;
    }
}