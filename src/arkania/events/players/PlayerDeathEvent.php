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

namespace arkania\events\players;

use arkania\utils\Utils;
use pocketmine\event\Listener;

final class PlayerDeathEvent implements Listener {

    /** @var array */
    public static array $backPosition = [];

    /**
     * @param \pocketmine\event\player\PlayerDeathEvent $event
     * @return void
     */
    public function onPlayerDeath(\pocketmine\event\player\PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if ($player->hasPermission('arkania:permission.back')){
            self::$backPosition[$player->getName()] = $player->getPosition();
            $player->sendMessage(Utils::getPrefix() . "§aVotre lieu de mort a été sauvegardé. Faites /back pour pouvoir vous y téléporter.");
        }
    }
}