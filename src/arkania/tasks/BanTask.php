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

namespace arkania\tasks;

use arkania\Core;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class BanTask extends Task {

    /** @var Player */
    private Player $player;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core, Player $player) {
        $this->core = $core;
        $this->player = $player;
    }

    public function onRun(): void {
        $player = $this->player;

        $staff = $this->core->sanction->getBanStaff($player->getName());
        $temps = $this->core->sanction->getBanTime($player->getName());
        $raison = $this->core->sanction->getBanRaison($player->getName());

        $timeRestant = $temps - time();
        $jours = intval(abs($timeRestant / 86400));
        $timeRestant = $timeRestant - ($jours * 86400);
        $heures = intval(abs($timeRestant / 3600));
        $timeRestant = $timeRestant - ($heures * 3600);
        $minutes = intval(abs($timeRestant / 60));
        $secondes = intval(abs($timeRestant - $minutes * 60));

        if($jours > 0)
            $format = $jours . ' jour(s) et ' .  $heures . ' heure(s)';
        else if($heures > 0)
            $format = $heures . ' heure(s) et ' . $minutes . ' minute(s)';
        else if($minutes > 0)
            $format = $minutes . ' minute(s) et ' . $secondes . ' seconde(s)';
        else
            $format = $secondes . 'seconde(s)';
        $player->disconnect("§7» §cVous êtes banni d'Arkania:\n§7» §cStaff: " . $staff . "\n§7» §cTemps: §e" . $format . "\n§7» §cRaison: §e" . $raison);
    }
}