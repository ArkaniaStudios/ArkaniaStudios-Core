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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\tasks\MaintenanceTask;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class MaintenanceCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('maintenance',
            'Maintenance - ArkaniaStudios',
        '/maintenance <on/off>');
        $this->setPermission('arkania:permission.maintenance');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (strtolower($args[0]) === 'on'){
            if ($this->core->serverStatus->getServerStatus(Utils::getServerName()) === '§6Maintenance'){
                $player->sendMessage(Utils::getServerName() . "§cLe serveur est déjà en maintenance.");
                return true;
            }

            $this->core->getScheduler()->scheduleRepeatingTask(new MaintenanceTask($this->core), 20);

        }elseif(strtolower($args[0]) === 'off'){
            if ($this->core->serverStatus->getServerStatus(Utils::getServerName()) !== '§6Maintenance'){
                $player->sendMessage(Utils::getPrefix() . "§cLe serveur n'est pas en maintenance.");
                return true;
            }

            $this->core->maintenance->setMaintenance(false);

        }else{
            return throw new InvalidCommandSyntaxException();
        }
        return true;
    }

}