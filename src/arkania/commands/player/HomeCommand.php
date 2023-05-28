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
use arkania\manager\HomeManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class HomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('home',
        'Permet de vous téléporter à vos homes',
        '/home <home>',
            ['h']);
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

        $homeManager = new HomeManager($player->getName());

        if (count($args) < 1){
            $allHome = $homeManager->getAllHome();

            $homeList = [];
            foreach ($allHome as $home){
                $homeList[] = $home['name'];
            }

            if (count($homeList) === null){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez aucun home.");
                return true;
            }

            $player->sendMessage(Utils::getPrefix() . "Voici la liste de vos homes (§e" . count($homeList) . "§f) : " . PHP_EOL . '- §e' . implode(PHP_EOL . '- §e', $homeList));
        }else{
            if (!$homeManager->existHome($args[0])){
                $player->sendMessage(Utils::getPrefix() . "§cCe home n'existe pas.");
                return true;
            }

            $homeManager->teleportHome($args[0]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté au home §e" . $args[0] . "§a.");
        }
        return true;
    }
}