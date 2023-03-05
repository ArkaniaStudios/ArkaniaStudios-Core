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

namespace arkania\tasks;

use arkania\commands\player\ServerSelectorCommand;
use arkania\data\ServerNameIds;
use arkania\utils\Utils;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class TransfertTask extends Task {

    /** @var int */
    private int $time = 5;

    /** @var bool */
    private bool $isAdmin;

    /** @var Player */
    private Player $player;

    /** @var int */
    private int $serverId;

    /** @var string */
    private string $serverType;

    public function __construct(string $serverType, int $serverId, Player $player, bool $isAdmin = false) {
        $this->serverType = $serverType;
        $this->serverId = $serverId;
        $this->player = $player;
        $this->isAdmin = $isAdmin;
    }

    public function onRun(): void {

        if (!$this->player->isOnline()) {
            unset(ServerSelectorCommand::$teleport[$this->player->getName()]);
            $this->getHandler()->cancel();
        }

        if ($this->time === 0 or $this->isAdmin === true){
            $this->player->sendMessage(Utils::getPrefix() . "§aTransfert en cours vers le serveur §2" . ServerNameIds::SERVER_TRANSFERT_NAME[$this->serverType][$this->serverId] . '§a...');
            unset(ServerSelectorCommand::$teleport[$this->player->getName()]);
            $this->player->transfer(ServerNameIds::SERVER_TRANSFERT_NAME[$this->serverType][$this->serverId]);
            $this->getHandler()->cancel();
        }
        $this->time--;
    }
}