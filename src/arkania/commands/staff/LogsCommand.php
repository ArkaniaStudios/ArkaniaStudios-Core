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
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class LogsCommand extends BaseCommand {

    /** @var Core  */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('logs',
        'Logs - Arkania',
        '/logs <on/off>');
        $this->setPermission('arkania:permission.logs');
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
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (strtolower($args[0]) === 'on'){

            if (isset($this->core->staff_logs[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cLes logs sont déjà activés.");
                return true;
            }
            $this->core->staff_logs[$player->getName()] = $player->getName();
            $player->sendMessage(Utils::getPrefix() . "Vous venez d'§aactiver §fles logs.");
        }elseif(strtolower($args[0]) === 'off'){
            if (!isset($this->core->staff_logs[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cLes logs ne sont pas activés.");
                return true;
            }
            unset($this->core->staff_logs[$player->getName()]);
            $player->sendMessage(Utils::getPrefix() . "Vous venez de §cdésactiver §fles logs.");
        }
        return true;
    }
}