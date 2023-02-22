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

class TempsBanCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('tempsban',
        'Tempsban - ArkaniaStudios',
        '/tempsban <player> <ip(oui/non)> <temps> <raison>',
        ['tban']);
        $this->setPermission('arkania:permission.tempsban');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole §r';

        if (count($args) < 3)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if ($args[2] > '30j') {
            if (!$player->hasPermission('arkania:permission.tempsban.bypass')) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas bannir une personne plus de §e30 jours§c. Si vous souhaitez dépasser cette limite merci de contacter un membre de l'administration.");
                return true;
            }
        }

        $format = null;
        $temps = null;
        $val = substr($args[2], -1);
        if ($val === 'j'){
            $temps = time() + ((int)$args[2]* 86400);
            $format = (int)$args[2] . ' jour(s)';
        }elseif($val === 'h'){
            $temps = time() + ((int)$args[2]* 3600);
            $format = (int)$args[2] . ' heure(s)';
        }elseif($val === 'm'){
            $temps = time() + ((int)$args[2]* 60);
            $format = (int)$args[2] . ' minute(s)';
        }elseif($val === 's'){
            $temps = time() + ((int)$args[2]);
            $format = (int)$args[2] . ' seconde(s)';
        }else
            return throw new InvalidCommandSyntaxException();

        if (!isset($args[3])) {
            if (!$player->hasPermission('arkania:permission.tempsban.bypass')) {
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes obligé de mettre une raison pour bannir une personne.Seul l'administration est autorisé à ne pas en mettre.");
                return true;
            }
            $raison = 'Aucun';
        }else{
            $raison = [];
            for ($i = 3;$i < count($args);$i++)
                $raison[] = $args[$i];
            $raison = implode(' ', $raison);
        }
        $this->core->sanction->addBan($target, $rank, $temps, $raison, Utils::getServerName());
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $target . "§c vient de se faire bannir du serveur par " . $rank . " §cdurant §e" . $format . "§c pour le motif §e" . $raison . "§c !");
        Utils::sendDiscordWebhook('**BANNISSEMENT**', '**' . $player->getName() . '** vient de bannir **' . $target . '** du serveur durant **' . $format . '** pour le motif **' . $raison . '**');

        return true;
    }

}