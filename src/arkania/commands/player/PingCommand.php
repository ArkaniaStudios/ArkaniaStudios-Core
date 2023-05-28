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
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class PingCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('ping',
        'Permet de voir votre ping ou celui d\'un autre joueur.',
        '/ping <player:optional>');
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

        if (count($args) === 0){
            $player->sendMessage(Utils::getPrefix() . "Vous avez actuellement " . $this->getPing($player) . "§f.");
        }else{
            $target = $this->core->getServer()->getPlayerByPrefix($args[0]);

            if (!$target instanceof Player){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
                return true;
            }
            $player->sendMessage(Utils::getPrefix() . RanksManager::getRanksFormatPlayer($target) . " a actuellement " . $this->getPing($target) . "§f.");
        }
        return true;
    }

    /**
     * @param Player $player
     * @return string
     */
    private function getPing(Player $player): string {
        $ping = $player->getNetworkSession()->getPing();
        if ($ping <= 30)
            $ping = '§a' . $ping . ' ping(s)';
        elseif($ping <= 60)
            $ping = '§2' . $ping . ' ping(s)';
        elseif($ping <= 100)
            $ping = '§6' . $ping . ' ping(s)';
        elseif($ping >= 101)
            $ping = '§c' . $ping . ' ping(s)';
        return $ping;
    }

}