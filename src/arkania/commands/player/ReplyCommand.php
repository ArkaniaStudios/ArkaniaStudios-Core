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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

class ReplyCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('reply',
        'Reply - ArkaniaStudios',
        '/reply <message>',
        ['r']);
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole';

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();

        if (empty(MsgCommand::$lastMessager[$player->getName()])){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de message récent.");
            return true;
        }

        $target = $this->core->getServer()->getPlayerExact(MsgCommand::$lastMessager[$player->getName()]);

        if (!$target instanceof Player) {
            $player->sendMessage(Utils::getPrefix() . "Ce joueur n'est pas connecté.");
            unset(MsgCommand::$lastMessager[$player->getName()]);
            return true;
        }

        $message = implode(' ', $args);

        $target->sendMessage("[§eMessage§f] §6" . $rank . " §7-> §6Vous §7§l» §r" . $message);
        $player->sendMessage("[§eMessage§f] §6Vous §7-> " . RanksManager::getRanksFormatPlayer($target) . " §7§l» §r" . $message);


        $this->sendStaffLogs(RanksManager::getRanksFormatPlayer($target) . ' -> ' . $rank . ": " . $message);
        return true;
    }
}