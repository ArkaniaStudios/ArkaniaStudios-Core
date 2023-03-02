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
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

final class BanListCommand extends BaseCommand {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('banlist',
        'Banlist - ArkaniaStudios',
        '/banlist <page>');
        $this->setPermission('arkania:permission.banlist');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$this->testPermission($player))
            return true;

        $banList = $this->core->getSanctionManager()->getAllBan();
        asort($banList);
        $maxpages = intval(abs(count($banList) / 10));
        $reste = count($banList) % 10;
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

        $player->sendMessage("§c- §fListe des personnes actuellement bannis (§e{$page}§f/§e{$maxpage}§f) §c-");
        $player->sendMessage(PHP_EOL);
        foreach ($banList as $name => $value) {
            if ($top === $fintop) break;
            if ($top >= $deptop)
                $player->sendMessage('§6#§e' . $top . ' §7» §e' . $name . ' §fdurant §e' . $this->tempsFormat($this->core->getSanctionManager()->getBanTime($name)) . '§f pour le motif §e' . $this->core->getSanctionManager()->getBanRaison($name));
            $top++;
        }
        return true;
    }

}