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
use arkania\utils\trait\Date;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

final class BanTask extends Task {
    use Date;

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

        $staff = $this->core->getSanctionManager()->getBanStaff($player->getName());
        $temps = $this->core->getSanctionManager()->getBanTime($player->getName());
        $raison = $this->core->getSanctionManager()->getBanRaison($player->getName());
        $player->disconnect("§7» §cVous êtes banni d'Arkania:\n§7» §cStaff: " . $staff . "\n§7» §cTemps: §e" . $this->tempsFormat($temps) . "\n§7» §cRaison: §e" . $raison);
    }
}