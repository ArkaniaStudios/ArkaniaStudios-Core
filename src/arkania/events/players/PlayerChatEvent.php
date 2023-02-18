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

use arkania\commands\player\FactionCommand;
use arkania\Core;
use arkania\manager\FactionManager;
use pocketmine\event\Listener;

class PlayerChatEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event) {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        /* Ranks */
        $event->setFormat($this->core->ranksManager->getChatFormat($player, $message));

        /* Faction */
        if (isset(FactionCommand::$faction_chat[$player->getName()]) || mb_substr($message, 0, 1) === '!'){

            $factionManager = new FactionManager();

            if ($factionManager->getFaction($player->getName()) === '...')
                return;


            $event->cancel();

            if (mb_substr($message, 0, 1) === '!')
                $factionMessage = mb_substr($message, 1);
            else
                $factionMessage = $message;

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionMessage($factionMessage, $player->getName());
        }
    }
}