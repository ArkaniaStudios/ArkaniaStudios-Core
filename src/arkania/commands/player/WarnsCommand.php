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
use pocketmine\player\Player;

final class WarnsCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('warns',
        'Warns - ArkaniaStudios',
        '/warns <player:optional>');
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

        if (count($args) < 1) {
            $warns = $this->core->getSanctionManager()->getWarns($player->getName());
            if ($warns === []){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez aucun warn.");
                return true;
            }

            $count = count($warns);
            $top = 1;
            $player->sendMessage("§c- §fVoici la liste de vos avertissements §c-" . PHP_EOL . PHP_EOL);
        }else {
            $warns = $this->core->getSanctionManager()->getWarns($args[0]);
            if ($warns === []) {
                $player->sendMessage(Utils::getPrefix() . "§e" . $args[0] . "§c n'a aucun warn.");
                return true;
            }
            $count = count($warns);
            $top = 1;
            $player->sendMessage("§c- §fVoici la liste des avertissements de §e" . $args[0] . " §c-" . PHP_EOL . PHP_EOL);
        }
        foreach ($warns as $value) {
            if ($top === $count + 1) break;
            $value = explode(":", $value);
            $staff = $value[0];
            $raison = $value[1];
            $date = $value[2];
            $player->sendMessage("§4#{$top}§c {$raison}§f par $staff §f(§e{$date}§f)");
            $top++;
        }
        return true;
    }
}