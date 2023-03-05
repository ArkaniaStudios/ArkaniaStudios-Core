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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class SetSpawnCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core){
        parent::__construct('setspawn',
        'Setspawn - ArkaniaStudios',
        '/setspawn');
        $this->setPermission('arkania:permission.setspawn');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     * @throws JsonException
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        $this->core->getSpawnManager()->setServerSpawn($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getWorld()->getDisplayName());
        $player->sendMessage(Utils::getPrefix() . "§aVous avez définis le spawn du serveur en : " . PHP_EOL . "x: §e" . $player->getPosition()->getX() . PHP_EOL . '§ay: §e' . $player->getPosition()->getY() . PHP_EOL . '§az: §e' . $player->getPosition()->getZ() . PHP_EOL . '§aMonde: §e' . $player->getWorld()->getDisplayName());
        return true;
    }
}