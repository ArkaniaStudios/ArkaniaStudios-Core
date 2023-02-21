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
use pocketmine\Server;

class SetRankCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('setrank',
        'Setrank - ArkaniaStudios',
        '/setrank <player> <rank>');
        $this->setPermission('arkania:permission.setrank');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool{
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if (!Utils::isValidArgument($target)) {
            $player->sendMessage(Utils::getPrefix() . "§cCe nom de joueur n'est pas valide.");
            return true;
        }

        if (!$this->core->ranksManager->existRank($args[1])){
            $player->sendMessage(Utils::getPrefix() . "§cCe grade n'existe pas !");
            return true;
        }

        $this->core->ranksManager->setRank($target, $args[1]);
        $vip_rank = [
            'Noble',
            'Hero',
            'Seigneur'
        ];

        if (!in_array($args[1], $vip_rank))
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§c" . $target . "§f vient de recevoir le grade §c" . $this->core->ranksManager->getRankColor($target) . " §f!");
        else
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§c" . $target . "§f vient d'acheter le grade §c" . $this->core->ranksManager->getRankColor($target) . "§f !");

        $this->sendStaffLogs($player->getName() . " vient de donner le grade " . $args[1] . " à $target");
        return true;
    }
}