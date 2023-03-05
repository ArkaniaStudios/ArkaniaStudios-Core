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
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

final class KickCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('kick',
            'Kick - ArkaniaStudios',
            '/kick <player> <raison:optional>');
        $this->setPermission('arkania:permission.kick');
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

        if (RanksManager::compareRank($player->getName(), $target->getName())){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas expulser cette personne car elle a un grade plus haut que vous.");
            return true;
        }

        if (!isset($args[1])) {
            if (!$player->hasPermission('arkania:permission.kick.bypass')){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez obligatoirement mettre une raison pour bannir une personne.");
                return true;
            }
            $raison = 'Aucun';
        } else {
            $raison = [];
            for ($i = 1; $i < count($args); $i++)
                $raison[] = $args[$i];
            $raison = implode(' ', $raison);
        }
        $target->disconnect("§7» §cVous avez été expulsé d'Arkania: \n§7» §cStaff: " . $rank . "\n§7» §cMotif: $raison");
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§c" . $target->getName() . "§c vient de se faire expulsé d'Arkania pour le motif $raison !");
        $this->sendStaffLogs($target->getName() . " vient de se faire kick par " . $rank . " pour le motif $raison");
        $this->sendDiscordWebhook('**KICK**', '・**' . $target->getName() . "** vient de se faire expulser d'arkania." . PHP_EOL . PHP_EOL . "*Informations*" . PHP_EOL . "- Expulser par **" . Utils::removeColorOnMessage($rank) . "**" . PHP_EOL . "- Server : **" . Utils::getServerName() . "**" . PHP_EOL . "- Motif : **" . $raison . "**", '・Sanction système - ArkaniaStudios', 0x8F3A84, WebhookData::KICK);

        return true;
    }
}