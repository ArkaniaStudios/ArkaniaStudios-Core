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

final class ListCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('list',
        'List - ArkaniaStudios',
        '/list');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        $online = '';
        $onlineCount = 0;
        $array = $this->core->getRanksManager()->classPlayersByRank();

        foreach ($this->core->getRanksManager()->getRanksList() as $rank) {
            if(isset($array[$this->core->getRanksManager()->getRankColorToString($rank)])) {
                foreach ($array[$this->core->getRanksManager()->getRankColorToString($rank)] as $target)
                    if((!($player instanceof Player) or $player->canSee($target))) {

                        $onlineCount++;
                        $online .= RanksManager::getRanksFormatPlayer($target) . '§7, ';

                    }
            }
        }

        $player->sendMessage(Utils::getPrefix() . "Voici la liste des personnes actuellement connecté sur le serveur (§e" . $onlineCount . '§f/§e' . $player->getServer()->getMaxPlayers() . "§f) :" . PHP_EOL . PHP_EOL . substr($online, 0, -2));
        return true;
    }

}