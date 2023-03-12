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

final class WarnCommand extends BaseCommand {
    use Date;
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('warn',
        'Warn - ArkaniaStudios',
        '/warn <player> <raison>');
        $this->setPermission('arkania:permission.warn');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            $ranks = '§cAdministrateur §f- §cConsole';
        else
            $ranks = RanksManager::getRanksFormatPlayer($player);

        if (!$this->testPermission($player))
            return true;

        if (count($args) < 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];
        $raison = [];
        for ($i = 1; $i < count($args);$i++)
            $raison[] = $args[$i];
        $raison = implode(' ', $raison);
        $date = $this->dateFormat();

        $value = "$ranks:$raison:$date";
        $this->core->getSanctionManager()->addPlayerWarn($target, $value);
        $player->sendMessage(Utils::getPrefix() . "§aVous venez de warn §e" . $target . "§a.");
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $target . "§c vient de se faire avertir par " . $ranks . " §cpour le motif §e" . $raison . "§c !");
        $this->sendDiscordWebhook('**WARN**', "**" . Utils::removeColorOnMessage($ranks) . "** vient de warn **" . $target . "**" . PHP_EOL . PHP_EOL . "- Joueur: **" . $target . "**" . PHP_EOL . "- Staff: **" . Utils::removeColorOnMessage($ranks) . "**" . PHP_EOL . "- Raison: **" . $raison . "**" . PHP_EOL . "- Serveur: **" . Utils::getServerName() . "**" . PHP_EOL . "- Date: **" . $this->dateFormat() . "**" . PHP_EOL . "- Nombre de warn: **" . count($this->core->getSanctionManager()->getWarns($target)) . "**", 'Système de sanction - ArkaniaStudios', 0x05E8B8, WebhookData::WARN);
        return true;
    }

}