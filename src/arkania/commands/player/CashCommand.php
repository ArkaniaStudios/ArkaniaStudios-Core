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
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

final class CashCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('cash',
        'Cash - ArkaniaStudios',
        '/cash <amount>');
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if ($player->hasPermission("arkania.cash.seigneur")){
            $cashLimite = 100000;
        }elseif($player->hasPermission("arkania.cash.hero")){
            $cashLimite = 75000;
        }elseif($player->hasPermission("arkania.cash.noble")){
            $cashLimite = 50000;
        }else{
            $cashLimite = 20000;
        }

        if (!Utils::isValidNumber($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
            return true;
        }

        if ($args[0] > $cashLimite){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas avoir un billet de plus de §e" . $cashLimite . "§c.");
            return true;
        }

        $money = $this->core->getEconomyManager()->getMoney($player->getName());
        if ($money < $args[0]){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent.");
            return true;
        }

        $item = ItemFactory::getInstance()->get(339);
        if (!$player->getInventory()->canAddItem($item)){
            $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez de place dans votre inventaire.");
            return true;
        }
        $item->setCustomName("§fBillet §c($args[0])");
        $player->sendMessage(Utils::getPrefix() . "Vous avez convertis §e$args[0] §fen billet.");
        $player->getInventory()->addItem($item);
        $this->core->getEconomyManager()->delMoney($player->getName(), $args[0]);
        return true;
    }
}