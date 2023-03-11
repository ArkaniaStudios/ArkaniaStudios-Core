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

namespace arkania\events\players;

use arkania\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

final class PlayerCommandProcessEvent implements Listener {

    /**
     * @param PlayerCommandPreprocessEvent $event
     * @return void
     */
    public function onPlayerCommandPrecess(PlayerCommandPreprocessEvent $event): void {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        $msg = explode(' ', trim($message));
        $m = substr("$message", 0, 1);
        $whitespace_check = substr($message, 1, 1);
        $slash_check = substr($msg[0], -1, 1);
        $quote_mark_check = substr($message, 1, 1) . substr($message, -1, 1);
        if ($m == '/'){
            if ($whitespace_check === ' ' || $whitespace_check === '\\' || $slash_check === '\\' || $quote_mark_check === '""'){
                $event->cancel();
                $player->sendMessage(Utils::getPrefix() . "§cMerci de ne pas mettre d'espace dans votre commande.");
            }
        }

    }
}