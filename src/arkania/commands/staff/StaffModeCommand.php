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

namespace arkania\commands\staff;

use arkania\commands\BaseCommand;
use arkania\Core;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class StaffModeCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('staffmode',
        'Staffmode - ArkaniaStudios',
        '/staffmode');
        $this->setPermission('arkania:permission.staffmode');
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

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        if ($this->core->getStaffManager()->isInStaffMode($player))
            $this->core->getStaffManager()->removeStaffMode($player);
        else
            $this->core->getStaffManager()->addStaffMode($player);
        return true;
    }
}