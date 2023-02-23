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

namespace arkania\jobs;

use pocketmine\player\Player;

interface Jobs {

    /**
     * @return void
     */
    public static function init(): void;

    /**
     * @return string
     */
    public function getJobName(): string;

    /**
     * @return array
     */
    public function getMaxXp(): array;

    /**
     * @param $playerName
     * @param int $value
     * @return void
     */
    public function addXp($playerName, int $value): void;

    /**
     * @param $playerName
     * @param int $value
     * @return void
     */
    public function delXp($playerName, int $value): void;

    /**
     * @param $playerName
     * @return int
     */
    public function getPlayerXp($playerName): int;

    /**
     * @param $playerName
     * @return int
     */
    public function getPlayerLevel($playerName): int;

    /**
     * @param $playerName
     * @return void
     */
    public function resetPlayerXp($playerName): void;

    /**
     * @param $playerName
     * @param int $level
     * @return void
     */
    public function checkRecompense($playerName, int $level): void;

    /**
     * @param Player $player
     * @return void
     */
    public function createJobsProfile(Player $player): void;

}