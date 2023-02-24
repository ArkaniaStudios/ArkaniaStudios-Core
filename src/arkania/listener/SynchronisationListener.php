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

namespace arkania\listener;

use arkania\Core;
use arkania\manager\SettingsManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SynchronisationListener implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();

        $settings = new SettingsManager($player);
        $settings->createUserSettings();

        $this->core->ranksManager->synchroJoinRank($player);
        $this->core->stats->createPlayerStats($player->getName());
        $this->core->stats->synchroJoinStats($player);

        $this->core->synchronisation->syncPlayer($player);
    }

    public function onPlayerQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();

        $this->core->ranksManager->synchroQuitRank($player);
        $this->core->stats->synchroQuitStats($player);

        $this->core->synchronisation->registerInv($player);

    }

    public function onPlayerKick(PlayerKickEvent $event) {
        $player = $event->getPlayer();

        $this->core->synchronisation->registerInv($player);

        $this->core->ranksManager->synchroQuitRank($player);
        $this->core->stats->synchroQuitStats($player);

    }
}