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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class NearCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('near',
        'Near - ArkaniaStudios',
        '/near');
        $this->setPermission('arkania:permission.near');
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

        if ($this->testPermission($player))
            return true;

        $boudingBox = $player->getBoundingBox();
        $count = 0;
        $playerName = '';
        $position = $player->getPosition();

        foreach ($player->getWorld()->getNearbyEntities($boudingBox->expandedCopy(75,75,75), $player) as $entity){
            if ($entity instanceof Player){
                $count++;
                $playerName .= PHP_EOL . '- §c' . RanksManager::getRanksFormatPlayer($entity) . ' §fà une distance de §e' . (int)$position->distance($entity->getPosition()) . ' blocs§f.';
            }
        }
        if ($count === 0){
            $player->sendMessage(Utils::getPrefix() . "§cIl n'y a aucun joueur dans un rayon de 75 blocs autours de vous !");
            return true;
        }

        $player->sendMessage(Utils::getPrefix() . "Il y a actuellement §e" . $count . " joueur(s) §fautours de vous :");
        $player->sendMessage(" ");
        $player->sendMessage($playerName);
        return true;
    }
}