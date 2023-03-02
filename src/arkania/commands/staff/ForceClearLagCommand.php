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
use arkania\tasks\ClearLagTask;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class ForceClearLagCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('forceclearlag',
        'Forceclearlag - ArkaniaStudios',
        '/forceclearlag');
        $this->setPermission('arkania:permission.forceclearlag');
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
            $rank = '§cAdministrateur §f- §cConsole';
        else
            $rank = RanksManager::getRanksFormatPlayer($player);

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        if (ClearLagTask::$time <= 20){
            $player->sendMessage(Utils::getPrefix() . "§cIl reste moins de §e20 secondes §cau clearlag. Vous ne pouvez donc pas le forcer.");
            return true;
        }

        $this->core->getScheduler()->scheduleRepeatingTask(new ClearLagTask($this->core, 20), 20);
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§cLe clearlag vient d'être forcé par $rank §cil aura donc lieu dans §e20 secondes §c!");
        return true;
    }
}