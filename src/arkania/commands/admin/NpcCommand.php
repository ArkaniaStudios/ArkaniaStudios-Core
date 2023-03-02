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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Loader;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class NpcCommand extends BaseCommand {

    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('npc',
        'Npc - ArkaniaStudios',
        '/npc <spawn> <npcId> <entityName>');
        $this->setPermission('arkania:permission.npc');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        $entityManager = new Loader($this->core);

        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 3)
            return throw new InvalidCommandSyntaxException();

        if (strtolower($args[0]) === 'create' || strtolower($args[0]) === 'spawn'){
            $entityIds = $entityManager->getEntityById($player->getLocation(), $args[1]);

            if (is_null($entityIds)){
                $player->sendMessage(Utils::getPrefix() . "§cCette entité n'existe pas.");
                return true;
            }
            $entityIds->setNpc();
            $entityIds->setCustomName($args[2]);
            $entityIds->setTaille(1);
            $entityIds->setNameTagAlwaysVisible();
            $entityIds->spawnToAll();
            $player->sendMessage(Utils::getPrefix() . "Vous avez bien fait spawn l'entité §c" . $args[1] . "§f.");
        }
        return true;
    }
}