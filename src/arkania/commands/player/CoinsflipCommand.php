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

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class CoinsflipCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('coinsflip',
        'Permet de parier un nombre d\'argent et de perdre ou gagner le double.',
        '/coinsflip <amount>');
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $economyAPI = $this->core->getEconomyManager();

        if ($args[0] > 25000){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas parrier plus que §e25K§c.");
            return true;
        }

        if ($economyAPI->getMoney($player->getName()) < $args[0]){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour parier cette somme.");
            return true;
        }

        $rand = mt_rand(1,100);

        if ($rand >= 30){
            $economyAPI->delMoney($player->getName(), (int)$args[0]);
            $player->sendMessage(Utils::getPrefix() . "§cVous avez perdu §e" . $args[0] . "§c.");
        }else{
            $economyAPI->addMoney($player->getName(), $args[0] * 2);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez gagné §e" . $args[0] * 2 . "§a.");
        }
        return true;
    }
}