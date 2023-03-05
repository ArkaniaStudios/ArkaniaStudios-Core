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
use arkania\data\WebhookData;
use arkania\manager\RanksManager;
use arkania\utils\trait\Date;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class TempsBanCommand extends BaseCommand {
    use Webhook;
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('tempsban',
        'Tempsban - ArkaniaStudios',
        '/tempsban <player> <temps> <raison>',
        ['tban']);
        $this->setPermission('arkania:permission.tempsban');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole §r';

        if (count($args) < 3)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if (RanksManager::compareRank($player->getName(), $target)){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas expulser cette personne car elle a un grade plus haut que vous.");
            return true;
        }

        if ($args[2] > '30j') {
            if (!$player->hasPermission('arkania:permission.tempsban.bypass')) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas bannir une personne plus de §e30 jours§c. Si vous souhaitez dépasser cette limite merci de contacter un membre de l'administration.");
                return true;
            }
        }
        $val = substr($args[1], -1);
        if ($val === 'j'){
            $temps = time() + ((int)$args[1]* 86400);
            $format = (int)$args[1] . ' jour(s)';
        }elseif($val === 'h'){
            $temps = time() + ((int)$args[1]* 3600);
            $format = (int)$args[1] . ' heure(s)';
        }elseif($val === 'm'){
            $temps = time() + ((int)$args[1]* 60);
            $format = (int)$args[1] . ' minute(s)';
        }elseif($val === 's'){
            $temps = time() + ((int)$args[1]);
            $format = (int)$args[1] . ' seconde(s)';
        }else
            return throw new InvalidCommandSyntaxException();

        if (!isset($args[2])) {
            if (!$player->hasPermission('arkania:permission.tempsban.bypass')) {
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes obligé de mettre une raison pour bannir une personne.Seul l'administration est autorisé à ne pas en mettre.");
                return true;
            }
            $raison = 'Aucun';
        }else{
            $raison = [];
            for ($i = 2;$i < count($args);$i++)
                $raison[] = $args[$i];
            $raison = implode(' ', $raison);
        }
        $this->core->getSanctionManager()->addBan($target, $rank, $temps, $raison, Utils::getServerName(), $this->dateFormat());
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $target . "§c vient de se faire bannir du serveur §cdurant §e" . $format . "§c pour le motif §e" . $raison . "§c !");
        $this->sendDiscordWebhook('**BANNISSEMENT**', '**' . $player->getName() . "** vient de bannir **" . $target . "** d'arkania." . PHP_EOL . PHP_EOL . "*Informations*" . PHP_EOL . "- Banni par **" . Utils::removeColorOnMessage($rank) . "**" . PHP_EOL . "- Durée : **" . $format . "**" . PHP_EOL . "- Server : **" . Utils::getServerName() . "**" . PHP_EOL . "- Raison : **" . $raison . "**", '・Sanction système - ArkaniaStudios', 0xE70235, WebhookData::BAN);
        if ($this->core->getServer()->getPlayerExact($target) instanceof Player)
            $this->core->getServer()->getPlayerExact($target)->disconnect("§7» §cVous avez été banni d'Arkania:\n§7» §cStaff: " . $rank . "\n§7» §cTemps: §e" . $format . "\n§7» §cMotif: §e" . $raison);
        return true;
    }
}