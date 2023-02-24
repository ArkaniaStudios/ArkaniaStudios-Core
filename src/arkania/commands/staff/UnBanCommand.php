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
use arkania\data\WebhookData;
use arkania\utils\trait\Date;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class UnBanCommand extends BaseCommand {
    use Date;
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('unban',
        'Unban - ArkaniaStudios',
        '/unban <playerName>',
        ['pardon']);
        $this->setPermission('arkania:permission.unban');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if ($this->core->sanction->isBan($target)) {
            $this->sendStaffLogs($player->getName() . ' vient de unban ' . $target);
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $target . " §avient d'être débanni du serveur !");
            $sanction = $this->core->sanction;
            $this->sendDiscordWebhook('**UNBAN**', "**" . $target . "** vient d'être débanni d'arkania. " . PHP_EOL . PHP_EOL . "*Informations*" . PHP_EOL . "- Banni par **" . Utils::removeColorOnMessage($sanction->getBanStaff($target)) . "**" . PHP_EOL . "- Temps restant : **" . $this->tempsFormat($sanction->getBanTime($target)) . "**" . PHP_EOL . "- Date du bannissement : **" . $sanction->getBanData($target) . "**" . PHP_EOL . "- Serveur : **" . $sanction->getBanServer($target) . "**" . PHP_EOL . "- Raison : **" . $sanction->getBanRaison($target) . "**", '・Système de sanction - ArkaniaStudios', 0xE85F05, WebhookData::BAN);
            $this->core->sanction->removeBan($target);
        }else
            $player->sendMessage(Utils::getPrefix() . "§cCette personne n'est pas banni.");
        return true;
    }
}