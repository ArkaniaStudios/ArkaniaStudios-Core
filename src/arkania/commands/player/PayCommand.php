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

use arkania\Core;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use arkania\commands\BaseCommand;

final class PayCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('pay',
        'Pay - ArkaniaStudios',
        '/pay <player> <amount>');
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

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $this->core->getServer()->getPlayerByPrefix($args[0]);

        if (!Utils::isValidNumber($args[1])){
            $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
            return true;
        }

        if (!$target instanceof Player){
            $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas connecté.");
            return true;
        }

        if (!$this->core->getEconomyManager()->getMoney($player->getName()) - $args[1] >= 0){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas les fonds requis pour effectuer cette transaction.");
            return true;
        }

        $this->core->getEconomyManager()->addMoney($target->getName(), (int)$args[1]);
        $this->core->getEconomyManager()->delMoney($player->getName(), (int)$args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez envoyé §e" . $args[1] . " §rà " . RanksManager::getRanksFormatPlayer($target));
        $target->sendMessage(Utils::getPrefix() . "Vous avez reçu §e" . $args[1] . " §r");
        return true;
    }

}