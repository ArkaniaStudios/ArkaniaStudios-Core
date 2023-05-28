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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

final class NightVisionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('nightvision',
        'Permet d\'activer un effet de vision nocturne.',
        '/nightvision');
        $this->setPermission('arkania:permission.nightvision');
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

        if ($player->getEffects()->has(VanillaEffects::NIGHT_VISION())) {
            $player->getEffects()->remove(VanillaEffects::NIGHT_VISION());
            $player->sendMessage(Utils::getPrefix() . "§cVous avez désactivé la vision nocturne.");
        } else {
            $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 100000000, 1, false));
            $player->sendMessage(Utils::getPrefix() . "§aVous avez activé la vision nocturne.");
        }
        return true;
    }
}