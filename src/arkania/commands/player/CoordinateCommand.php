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
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;

final class CoordinateCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('coordinates',
        'Coordinates - ArkaniaStudios',
        '/coordinates <on/off>',
        ['xyz', 'coord', 'co', 'coordinate', 'coordonne']);
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

        if (strtolower($args[0]) === 'on'){
            $gm = new GameRulesChangedPacket();
            $gm->gameRules = ["showcoordinates" => new BoolGameRule(true, false)];
            $player->getNetworkSession()->sendDataPacket($gm);
            $player->sendMessage(Utils::getPrefix() . "§aVous venez d'activer les coordonnées.");
        }elseif(strtolower($args[0]) === 'off'){
            $gm = new GameRulesChangedPacket();
            $gm->gameRules = ["showcoordinates" => new BoolGameRule(false, false)];
            $player->getNetworkSession()->sendDataPacket($gm);
            $player->sendMessage(Utils::getPrefix() . "§cVous venez de désactiver les coordonnées.");
        }else
            return throw new InvalidCommandSyntaxException();
        return true;
    }

}