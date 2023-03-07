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
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

final class SetHomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('sethome',
        'Sethome - ArkaniaStudios',
        '/sethome <homeName>');
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     * @throws JsonException
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (!Utils::isValidArgument($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cLe nom de ce home n'est pas valide.");
            return true;
        }

        if (strlen($args[0]) > 10){
            $player->sendMessage(Utils::getPrefix() . "§cLe nom de votre home ne peut pas avoir plus de §e10 caractères§c.");
            return true;
        }

        $homeManager = new HomeManager($player->getName());

        if ($homeManager->existHome($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe home existe déjà.");
            return true;
        }

        if ($player->hasPermission('arkania:permission.seigneur'))
            $homeLimite = 8;
        elseif($player->hasPermission('arkania:permission.hero'))
            $homeLimite = 6;
        elseif($player->hasPermission('arkania:permission.noble'))
            $homeLimite = 4;
        else
            $homeLimite = 2;

        if ($homeManager->countHome() >= $homeLimite){
            $player->sendMessage(Utils::getPrefix() . "§cVous avez atteins votre limite de home qui est de §e" . $homeLimite . "§c.");
            return true;
        }

        if ($player->getWorld()->getFolderName() !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre de home dans ce monde.");
            return true;
        }

        $homeManager->setHome($args[0], new Position($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getWorld()));
        $player->sendMessage(Utils::getPrefix() . "§aVous venez de définir le home §e" . $args[0] . "§a.");
        return true;
    }
}