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
use arkania\utils\trait\Date;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

final class UnMuteCommand extends BaseCommand {
    use Date;
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('unmute',
        'Unmute - ArkaniaStudios',
        '/unmute <player>');
        $this->setPermission('arkania:permission.unmute');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if ($this->core->getSanctionManager()->isMute($target)) {
            $this->sendStaffLogs($player->getName() . ' vient de unmute ' . $target);
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $target . " §avient d'être unmute du serveur !");
            $sanction = $this->core->getSanctionManager();
            $this->sendDiscordWebhook('**UNMUTE**', "**" . $target . "** vient d'être unmute d'arkania. " . PHP_EOL . PHP_EOL . "*Informations*" . PHP_EOL . "- Mute par **" . Utils::removeColorOnMessage($sanction->getMuteStaff($target)) . "**" . PHP_EOL . "- Temps restant : **" . $this->tempsFormat($sanction->getMuteTime($target)) . "**" . PHP_EOL . "- Date du mute : **" . $sanction->getMuteDate($target) . "**" . PHP_EOL . "- Serveur : **" . $sanction->getMuteServer($target) . "**" . PHP_EOL . "- Raison : **" . $sanction->getMuteRaison($target) . "**", '・Système de sanction - ArkaniaStudios', 0xE85F05, WebhookData::MUTE);
            $this->core->getSanctionManager()->removeMute($target);
        }else
            $player->sendMessage(Utils::getPrefix() . "§cCette personne n'est pas mute.");
        return true;
    }
}