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
    public function resetPlayerJob($playerName): void;

    /**
     * @param $playerName
     * @param int $level
     * @return void
     */
    public function sendReward($playerName, int $level): void;

    /**
     * @param $playerName
     * @return void
     */
    public function createJobsProfile($playerName): void;

    /**
     * @param $playerName
     * @param $value
     * @return bool
     */
    public function canRecupReward($playerName, $value): bool;

    public function synchroJobsOnJoin(Player $player): void;

    public function synchroJobsOnQuit(Player $player): void;

}