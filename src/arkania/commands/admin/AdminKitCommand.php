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
use arkania\utils\Utils;
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class AdminKitCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('adminkit',
        'Adminkit - ArkaniaStudios',
        '/adminkit <reset:optional>');
        $this->setPermission('arkania:permission.adminkit');
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

        if (count($args) < 1)
            $this->core->getFormManager()->sendKitForm($player, true);
        else{
            if ($args[0] === 'reset'){
                if (!isset($args[1])) {
                    $player->sendMessage(Utils::getPrefix() . "§aOK !");
                    $this->core->getKitsManager()->resetCooldown($player);
                }else{
                    $target = $this->core->getServer()->getPlayerByPrefix($args[1]);
                    if (!$target instanceof Player){
                        $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
                        return true;
                    }
                    $this->core->getKitsManager()->resetCooldown($target);
                    $player->sendMessage(Utils::getPrefix() . "§aOK !");
                }
            }else
                return throw new InvalidCommandSyntaxException();
        }
        return true;
    }
}