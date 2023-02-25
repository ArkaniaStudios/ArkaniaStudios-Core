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

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\manager\FactionManager;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoCommand extends BaseCommand {
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

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        $faction = new FactionManager();
        $stats = $this->core->stats;

        if (count($args) === 0){
            $player->sendMessage(Utils::getPrefix() . "Voici les informations vous concernant :" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->ranksManager->getRankColor($player->getName()) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($player->getName()) . PHP_EOL . "§f- Argent : §e" . $this->core->economyManager->getMoney($player->getName()) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($player->getName()) . PHP_EOL . "§f- Temps de jeu : §e" . $this->tempsFormat($stats->getRealPlayTime($player->getName())) . PHP_EOL . PHP_EOL . "§f- Status : " . $this->core->stats->getServerConnection($player->getName()));
        }else{
            $target = $args[0];
            if (!$this->core->ranksManager->existPlayer($target)){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur ne s'est jamais connecté au serveur.");
                return true;
            }

            $player->sendMessage(Utils::getPrefix() . "Voici les informations concernant §e" . $target . "§f:" . PHP_EOL . PHP_EOL . "- Grade : " . $this->core->ranksManager->getRankColor($target) . PHP_EOL . "§f- Faction: §e" . $faction->getFaction($target) . PHP_EOL . "§f- Argent : §e" . $this->core->economyManager->getMoney($target) . "" . PHP_EOL . PHP_EOL . "§f- Inscription : §e" . $stats->getInscription($target) . PHP_EOL . "§f- Temps de jeu : §e" . $this->tempsFormat($stats->getRealPlayTime($target)) . PHP_EOL . PHP_EOL . "§f- Status : " . $this->core->stats->getServerConnection($target));

        }
        return true;
    }
}