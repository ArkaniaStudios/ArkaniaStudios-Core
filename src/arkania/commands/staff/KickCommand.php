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

namespace arkania\commands\staff;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

class KickCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('kick',
            'Kick - ArkaniaStudios',
            '/kick <player> <raison:optional>');
        $this->setPermission('arkania:permission.kick');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole';


        if (!$this->testPermission($player))
            return true;

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);

        if (!$target instanceof Player) {
            $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            return true;
        }

        if (!isset($args[1])) {
            $target->disconnect("§7» §cVous avez été expulsé d'Arkania: \n§7» §cStaff: " . $rank . "\n§7» §cRaison: Aucune");
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§c" . $target->getName() . "§c vient de se faire expulsé d'Arkania pour le motif Aucun !");
            $this->sendStaffLogs($target->getName() . " vient de se faire kick par " . $rank);
        } else {
            $raison = [];
            for ($i = 1; $i < count($args); $i++)
                $raison[] = $args[$i];
            $raison = implode(' ', $raison);
            $target->disconnect("§7» §cVous avez été expulsé d'Arkania: \n§7» §cStaff: " . $rank . "\n§7» §cRaison: $raison");
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§c" . $target->getName() . "§c vient de se faire expulsé d'Arkania pour le motif $raison !");
            $this->sendStaffLogs($target->getName() . " vient de se faire kick par " . $rank . " pour le motif $raison");
        }
        return true;
    }
}