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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\libs\muqsit\invmenu\InvMenu;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class CraftCommand extends BaseCommand {

    public const INV_MENU_TYPE_WORKBENCH = 'portablecrafting:workbench';

    private function workbench(): InvMenu{
        return InvMenu::create(self::INV_MENU_TYPE_WORKBENCH);
    }

    public function __construct() {
        parent::__construct('craft',
        'Craft - ArkaniaStudios',
        '/craft');
        $this->setPermission('arkania:permission.craft');
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

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        $this->workbench()->send($player);
        return true;
    }

}