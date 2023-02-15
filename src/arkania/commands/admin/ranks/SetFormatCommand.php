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

class SetFormatCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('setformat',
        'Setformat - ArkaniaStudios',
        '/setformat <rank> <format>');
        $this->setPermission('arkania:permission.setformat');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) < 2)
            return throw new InvalidCommandSyntaxException();

        if (!$this->core->ranksManager->existRank($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe grade n'existe pas.");
            return true;
        }

        $format = [];
        for ($i = 1;$i < count($args);$i++)
            $format[] = $args[$i];
        $format = implode(' ', $format);

        $this->core->ranksManager->updateRankFormat($args[0], $format);
        $player->sendMessage(Utils::getPrefix() . "§aVous avez bien modifier le format du grade §2" . $args[0] . "§a.");

        $this->sendStaffLogs($player->getName() . " vient de modifier le format du grade " . $args[0] . " en $format.");
        return true;
    }

}