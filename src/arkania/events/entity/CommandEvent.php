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

namespace arkania\events\entity;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\player\Player;

final class CommandEvent implements Listener {

    /** @var array */
    private array $bannedCommands = [
        'spawn',
        'lobby',
        'selector',
        'tpa',
        'tpaccept',
        'tpahere',
        'f',
        'home',
        'sethome'
    ];

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param \pocketmine\event\server\CommandEvent $event
     * @return void
     */
    public function onCommand(\pocketmine\event\server\CommandEvent $event): void {
        $player = $event->getSender();

        if ($player instanceof Player){
            if ($this->core->getStaffManager()->isFreeze($player) || $this->core->getStaffManager()->isInStaffMode($player)){
                $args = [];
                if ($event->getCommand() !== 'msg') {
                    preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $event->getCommand(), $matches);
                    foreach ($matches[0] as $k => $_) {
                        for ($i = 1; $i <= 2; ++$i) {
                            if ($matches[$i][$k] !== '') {
                                $args[$k] = $i === 1 ? stripslashes($matches[$i][$k]) : $matches[$i][$k];
                                break;
                            }
                        }
                    }
                    $input = strtolower(trim(implode(" ", $args)));
                    foreach ($this->bannedCommands as $command) {
                        if (str_starts_with($input, $command)) {
                            $event->cancel();
                            if ($this->core->getStaffManager()->isInStaffMode($player))
                                $player->sendMessage(Utils::getPrefix() . '§cVous ne pouvez pas faire ce genre de commande en étant en staffmode.');
                            else
                                $player->sendMessage(Utils::getPrefix() . '§cVous ne pouvez pas faire ce genre de commande en étant freeze.');
                            return;
                        }
                    }
                }
            }
        }
    }
}