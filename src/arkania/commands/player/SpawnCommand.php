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
use arkania\tasks\TeleportTask;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class SpawnCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    /** @var array */
    public static array $teleport = [];

    public function __construct(Core $core) {
        parent::__construct('spawn',
        'Téléporte au spawn du serveur',
        '/spawn',
        ['hub']);
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

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        if (!$this->core->getSpawnManager()->existSpawn()){
            $player->sendMessage(Utils::getPrefix() . "§cLe spawn n'a pas été définit par un administrateur. Merci d'en contacter un.");
            return true;
        }

        $this->core->getScheduler()->scheduleRepeatingTask(new TeleportTask($this->core, $player, 'spawn', $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), 20);
        return true;
    }
}