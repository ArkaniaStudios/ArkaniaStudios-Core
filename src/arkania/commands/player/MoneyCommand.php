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

namespace arkania\commands\player;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use arkania\commands\BaseCommand;

class MoneyCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('money',
        'Money - ArkaniaStudios',
        '/money <player:optional>');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (count($args) > 1)
            return throw new InvalidCommandSyntaxException();

        if (!isset($args[0])){
            $money = $this->core->economyManager->getMoney($player->getName());
            $player->sendMessage(Utils::getPrefix() . "Vous avez actuellement §c" . $money . "§f.");
        }else{
            $target = $args[0];
            $money = $this->core->economyManager->getMoney($target);
            $player->sendMessage(Utils::getPrefix() . "§c" . $target . " a actuellement §c" . $money . "§f.");
        }
        return true;
    }
}