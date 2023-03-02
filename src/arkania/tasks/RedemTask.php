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

namespace arkania\tasks;

use arkania\commands\staff\RedemCommand;
use arkania\utils\Utils;
use pocketmine\scheduler\Task;
use pocketmine\Server;

final class RedemTask extends Task {

    /** @var RedemCommand */
    private RedemCommand $redemCommand;

    /** @var int */
    private int $time = 30;

    public function __construct(RedemCommand $redemCommand) {
        $this->redemCommand = $redemCommand;
    }

    public function onRun(): void {
        $time = $this->time;
        $redemCommand = $this->redemCommand;
        $server = Server::getInstance();

        $this->time--;
        var_dump($this->time--);
        if ($time == 0){
            foreach ($server->getOnlinePlayers() as $player) {
                $player->sendMessage(Utils::getPrefix() . "§cRedémarrage du serveur " . Utils::getServerName() . " §c!");
                $player->transfer('lobby1');
            }
            if (isset($redemCommand->redem['redemStatus']))
                unset($redemCommand->redem['redemStatus']);
            $server->shutdown();
        }elseif($time == 30)
            $server->broadcastMessage(Utils::getPrefix() . "§cLe serveur redémarrage dans 30 secondes !");
        elseif($time == 16)
            $server->broadcastMessage(Utils::getPrefix() . "§aSauvegarde des données du serveur en cours...");
        elseif($time == 10)
            $server->broadcastMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
        elseif($time == 6)
            $server->broadcastMessage(Utils::getPrefix() . "§cRedémarrage dans 5 secondes");
    }
}