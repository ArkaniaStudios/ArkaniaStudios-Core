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

namespace arkania\commands\player;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use arkania\commands\BaseCommand;

final class MoneyCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('money',
        'Money - ArkaniaStudios',
        '/money <player:optional>');
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

        if (count($args) > 1)
            return throw new InvalidCommandSyntaxException();

        if (!isset($args[0])){
            $money = $this->core->getEconomyManager()->getMoney($player->getName());
            $player->sendMessage(Utils::getPrefix() . "Vous avez actuellement §e" . $money . "§f.");
        }else{
            $target = $args[0];
            $money = $this->core->getEconomyManager()->getMoney($target);
            $player->sendMessage(Utils::getPrefix() . "§e" . $target . " §fa actuellement §e" . $money . "§f.");
        }
        return true;
    }
}