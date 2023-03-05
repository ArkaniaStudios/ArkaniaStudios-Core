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
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class TpacceptCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('tpaccept',
        'Tpaccept - ArkaniaStudios',
        '/tpaccept');
        $this->core = $core;
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

        if ($this->core->getTeleportManager()->hasRequest($player)){
            if ($this->core->getTeleportManager()->teleporteeStillOnline($player)){
                $target = $this->core->getTeleportManager()->getTeleportee($player);
                if (!$this->core->getTeleportManager()->isTpaHereRequest($player)){
                    $target->teleport($player->getLocation());
                }else{
                    $player->teleport($target->getLocation());
                }
                $this->core->getTeleportManager()->destroyRequest($player);
            }else{
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            }
        }else{
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de demande de téléportation.");
        }
        return true;
    }
}