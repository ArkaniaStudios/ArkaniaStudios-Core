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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

final class FeedCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('feed',
        'Feed - ArkaniaStudios',
        '/feed <player:optional>',
        ['eat']);
        $this->setPermission('arkania:permission.feed');
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

        if (count($args) < 1){
            if ($player->getHungerManager()->getFood() >= 20) {
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas besoin d'être nourris.");
                return true;
            }

            $player->getHungerManager()->setFood(20);
            $player->getHungerManager()->setSaturation(20);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez été nourris.");
        }else{

            if (!$player->hasPermission('arkania:permission.feed.other')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de feed une autre permission.");
                return true;
            }

            $target = Server::getInstance()->getPlayerByPrefix($args[0]);

            if (!$target instanceof Player){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
                return true;
            }

            $target->getHungerManager()->setFood(20);
            $target->getHungerManager()->setSaturation(20);
            $target->sendMessage(Utils::getPrefix() . "§aVous avez été nourris par " . RanksManager::getRanksFormatPlayer($target) . "§a.");
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien nourris " . RanksManager::getRanksFormatPlayer($target) . "§a.");
        }
        return true;
    }
}