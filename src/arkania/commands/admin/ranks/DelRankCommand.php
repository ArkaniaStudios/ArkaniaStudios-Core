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

class DelRankCommand extends BaseCommand {

    /** @var Core  */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('delrank',
        'Delrank - ArkaniaStudios',
        '/delrank <rankName>');
        $this->setPermission('arkania:permission.delrank');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (!$this->core->ranksManager->existRank($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe grade n'existe pas.");
            return true;
        }

        if ($args[0] === 'Joueur'){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas supprimer le grade par default.");
            return true;
        }

        $this->core->ranksManager->delRank($args[0]);
        $player->sendMessage(Utils::getPrefix() . "§aVous venez de supprimer le grade §2" . $args[0] . "§a.");
        $this->sendStaffLogs($player->getName() . ' vient de supprimer le grade ' . $args[0] . '.');
        return true;
    }
}