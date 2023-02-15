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
use arkania\tasks\RedemTask;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class RedemCommand extends BaseCommand {

    /** @var Core  */
    private Core $core;

    /** @var array  */
    public array $redem = [];

    public function __construct(Core $core) {
        parent::__construct('redem',
        'Redem - ArkaniaStudios',
        '/redem');
        $this->setPermission("arkania:permission.redem");
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        $this->redem['redemStatus'] = true;
        $this->core->getScheduler()->scheduleRepeatingTask(new RedemTask($this), 20*2);
        return true;
    }
}