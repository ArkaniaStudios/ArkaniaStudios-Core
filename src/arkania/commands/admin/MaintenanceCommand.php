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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\data\WebhookData;
use arkania\manager\RanksManager;
use arkania\tasks\MaintenanceTask;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class MaintenanceCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('maintenance',
            'Permet d\'activer ou non la maintenance sur le serveur',
        '/maintenance <on/off>');
        $this->setPermission('arkania:permission.maintenance');
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (strtolower($args[0]) === 'on'){
            if ($this->core->getServerStatus()->getServerStatus(Utils::getServerName()) === '§6Maintenance'){
                $player->sendMessage(Utils::getServerName() . "§cLe serveur est déjà en maintenance.");
                return true;
            }

            $this->core->getScheduler()->scheduleRepeatingTask(new MaintenanceTask($this->core, $rank), 20);

        }elseif(strtolower($args[0]) === 'off'){
            if ($this->core->getServerStatus()->getServerStatus(Utils::getServerName()) !== '§6Maintenance'){
                $player->sendMessage(Utils::getPrefix() . "§cLe serveur n'est pas en maintenance.");
                return true;
            }

            $this->sendDiscordWebhook('**MAINTENANCE**', "La maintenance vient d'être désactivé sur un serveur." . PHP_EOL . PHP_EOL . "- Server : **" . Utils::getServerName() . "**" . PHP_EOL . "- Staff : " . Utils::removeColorOnMessage($rank), 'Maintenance Système - ArkaniaStudios', 0xEFA, WebhookData::MAINTENANCE);
            $this->core->getMaintenanceManager()->setMaintenance(false);

        }else
            return throw new InvalidCommandSyntaxException();
        return true;
    }

}