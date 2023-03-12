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
use arkania\data\WebhookData;
use arkania\manager\RanksManager;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

final class SetRankCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('setrank',
        'Setrank - ArkaniaStudios',
        '/setrank <player> <rank>');
        $this->setPermission('arkania:permission.setrank');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool{

        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole';

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if (!Utils::isValidArgument($target)) {
            $player->sendMessage(Utils::getPrefix() . "§cCe nom de joueur n'est pas valide.");
            return true;
        }

        if (!$this->core->getRanksManager()->existRank($args[1])){
            $player->sendMessage(Utils::getPrefix() . "§cCe grade n'existe pas !");
            return true;
        }

        $this->sendDiscordWebhook('**SETRANK**', "Le grade de **$target** vient d'être changé. " . PHP_EOL . PHP_EOL . "Staff: " . Utils::removeColorOnMessage($rank) . PHP_EOL . "Ancien grade: " . $this->core->getRanksManager()->getPlayerRank($target) . PHP_EOL . "Nouveau grade : " . $args[1], '・Système de grade - ArkaniaStudios', 0xF89, WebhookData::SETRANK);
        $this->core->getRanksManager()->setRank($target, $args[1]);
        $vip_rank = [
            'Noble',
            'Hero',
            'Seigneur'
        ];

        if (!in_array($args[1], $vip_rank))
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§c" . $target . "§f vient de recevoir le grade §c" . $this->core->getRanksManager()->getRankColorToString($args[1]) . " §f!");
        else
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§c" . $target . "§f vient d'acheter le grade §c" . $this->core->getRanksManager()->getRankColorToString($args[1]) . "§f !");

        $this->sendStaffLogs($player->getName() . " vient de donner le grade " . $args[1] . " à $target");
        return true;
    }
}