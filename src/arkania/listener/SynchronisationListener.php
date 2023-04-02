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

namespace arkania\listener;

use arkania\Core;
use arkania\manager\SettingsManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class SynchronisationListener implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $settings = new SettingsManager($player);
        $settings->createUserSettings();

        $this->core->getRanksManager()->synchroJoinRank($player);
        $this->core->getStatsManager()->createPlayerStats($player);
        $this->core->getStatsManager()->synchroJoinStats($player);

        $this->core->getSynchronisationManager()->syncPlayer($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        $this->core->getStatsManager()->synchroQuitStats($player);
        $this->core->getRanksManager()->synchroQuitRank($player);
        $this->core->getSynchronisationManager()->registerInv($player);
    }

    /**
     * @param PlayerKickEvent $event
     * @return void
     */
    public function onPlayerKick(PlayerKickEvent $event): void {
        $player = $event->getPlayer();

        $this->core->getStatsManager()->synchroQuitStats($player);
        $this->core->getSynchronisationManager()->registerInv($player);
        $this->core->getRanksManager()->synchroQuitRank($player);
        //$this->core->getJobsManager()->getMineurJobs()->synchroJobsOnQuit($player);
    }
}