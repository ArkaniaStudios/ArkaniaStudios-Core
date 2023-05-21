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
use arkania\manager\FactionManager;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class InfoCommand extends BaseCommand {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('info',
        'Info - ArkaniaStudios',
        '/info <player:optional>',
        ['playerinfo', 'infos']);
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

        $faction = new FactionManager();
        $stats = $this->core->getStatsManager();

        if (count($args) === 0){
            $player->sendMessage(Utils::getPrefix() . "Voici les informations vous concernant :" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->getRanksManager()->getRankColor($player->getName()) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($player->getName()) . PHP_EOL . "§f- Argent : §e" . $this->core->getEconomyManager()->getMoney($player->getName()) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($player->getName()) . PHP_EOL . "§f- Temps de jeu : §eDésactivé" . PHP_EOL . PHP_EOL . "§f- Status : " . $stats->getServerConnection($player->getName()));
        }else{
            $player->sendMessage(Utils::getPrefix() . '§cDésactivé temporairement.');
            return true;
            /*$target = $args[0];
            if (!$this->core->getRanksManager()->existPlayer($target)){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur ne s'est jamais connecté au serveur.");
                return true;
            }

            $player->sendMessage(Utils::getPrefix() . "Voici les informations concernant §e" . $target . "§f:" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->getRanksManager()->getRankColor($target) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($target) . PHP_EOL . "§f- Argent : §e" . $this->core->getEconomyManager()->getMoney($target) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($target) . PHP_EOL . "§f- Temps de jeu : §e" . $this->tempsFormat(abs($stats->getTime($target))) . PHP_EOL . PHP_EOL . "§f- Status : " . $stats->getServerConnection($target));*/

        }
        return true;
    }
}