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
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

final class ClassementCommand extends BaseCommand {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('classement',
        'Classement - ArkaniaStudios',
        '/classement <type> <page:optional>');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();

        if (strtolower($args[0]) === "money" || strtolower($args[0]) === "argent"){
            $moneyJson = $this->core->getEconomyManager()->getAllMoney();
            arsort($moneyJson);
            $maxpages = intval(abs(count($moneyJson) / 10));
            $reste = count($moneyJson) % 10;
            if ($reste > 0) {
                $maxpage = $maxpages + 1;
            } else {
                $maxpage = $maxpages;
            }
            if ((isset($args[1])) and (!(is_numeric($args[1])))) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            if (isset($args[1])) $args[1] = intval($args[1]);
            if (!isset($args[1]) or $args[1] == 1) {
                $deptop = 1;
                $fintop = 11;
                $page = 1;
            } else {
                $deptop = (($args[1] - 1) * 10) + 1;
                $fintop = (($args[1] - 1) * 10) + 11;
                $page = $args[1];
            }
            if ($page > $maxpage) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            $top = 1;

            $player->sendMessage("§c- §fListe des joueurs avec le plus d'argent [§e{$page}§f/§e{$maxpage}§f] §c-");
            $player->sendMessage(PHP_EOL);
            foreach ($moneyJson as $name => $money) {
                if ($top === $fintop) break;
                if ($top >= $deptop) {
                    $player->sendMessage("§c#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $money . "");
                }
                $top++;
            }
            $player->sendMessage(PHP_EOL);
        }elseif (strtolower($args[0]) === "kill" || strtolower($args[0]) === "kills"){
            $killJson = $this->core->getStatsManager()->getAllKill();
            arsort($killJson);
            $maxpages = intval(abs(count($killJson) / 10));
            $reste = count($killJson) % 10;
            if ($reste > 0) {
                $maxpage = $maxpages + 1;
            } else {
                $maxpage = $maxpages;
            }
            if ((isset($args[1])) and (!(is_numeric($args[1])))) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            if (isset($args[1])) $args[1] = intval($args[1]);
            if (!isset($args[1]) or $args[1] == 1) {
                $deptop = 1;
                $fintop = 11;
                $page = 1;
            } else {
                $deptop = (($args[1] - 1) * 10) + 1;
                $fintop = (($args[1] - 1) * 10) + 11;
                $page = $args[1];
            }
            if ($page > $maxpage) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            $top = 1;

            $player->sendMessage("§c- §fListe des joueurs avec le plus de kill [§e{$page}§f/§e{$maxpage}§f] §c-");
            $player->sendMessage(PHP_EOL);
            foreach ($killJson as $name => $money) {
                if ($top === $fintop) break;
                if ($top >= $deptop) {
                    $player->sendMessage("§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $money . "kill(s)");
                }
                $top++;
            }
            $player->sendMessage(PHP_EOL);
        }elseif (strtolower($args[0]) === "death" || strtolower($args[0]) === "mort"){
            $deathJson = $this->core->getStatsManager()->getAllDeath();
            arsort($deathJson);
            $maxpages = intval(abs(count($deathJson) / 10));
            $reste = count($deathJson) % 10;
            if ($reste > 0) {
                $maxpage = $maxpages + 1;
            } else {
                $maxpage = $maxpages;
            }
            if ((isset($args[1])) and (!(is_numeric($args[1])))) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            if (isset($args[1])) $args[1] = intval($args[1]);
            if (!isset($args[1]) or $args[1] == 1) {
                $deptop = 1;
                $fintop = 11;
                $page = 1;
            } else {
                $deptop = (($args[1] - 1) * 10) + 1;
                $fintop = (($args[1] - 1) * 10) + 11;
                $page = $args[1];
            }
            if ($page > $maxpage) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            $top = 1;

            $player->sendMessage("§c- §fListe des joueurs avec le plus de mort(s) [§e{$page}§f/§e{$maxpage}§f] §c-");
            $player->sendMessage(PHP_EOL);
            foreach ($deathJson as $name => $money) {
                if ($top === $fintop) break;
                if ($top >= $deptop) {
                    $player->sendMessage("§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $money . "mort(s)");
                }
                $top++;
            }
            $player->sendMessage(PHP_EOL);
        }elseif(strtolower($args[0]) === 'time'){
            $temps = $this->core->getStatsManager()->getAllTime();
            arsort($temps);
            $maxpages = intval(abs(count($temps) / 10));
            $reste = count($temps) % 10;
            if ($reste > 0) {
                $maxpage = $maxpages + 1;
            } else {
                $maxpage = $maxpages;
            }
            if ((isset($args[1])) and (!(is_numeric($args[1])))) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            if (isset($args[1])) $args[1] = intval($args[1]);
            if (!isset($args[1]) or $args[1] == 1) {
                $deptop = 1;
                $fintop = 11;
                $page = 1;
            } else {
                $deptop = (($args[1] - 1) * 10) + 1;
                $fintop = (($args[1] - 1) * 10) + 11;
                $page = $args[1];
            }
            if ($page > $maxpage) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            $top = 1;

            $player->sendMessage("§c- §fListe des joueurs avec le plus de temps de jeu [§e{$page}§f/§e{$maxpage}§f] §c-");
            $player->sendMessage(PHP_EOL);
            foreach ($temps as $name => $time) {
                if ($top === $fintop) break;
                if ($top >= $deptop) {
                    $player->sendMessage("§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $this->tempsformat($time));
                }
                $top++;
            }
            $player->sendMessage(PHP_EOL);
        }
        return true;
    }
}