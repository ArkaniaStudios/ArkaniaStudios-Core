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

namespace arkania\tasks;

use arkania\Core;
use arkania\data\SettingsNameIds;
use arkania\entity\entities\VillagerEntity;
use arkania\manager\SettingsManager;
use arkania\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\Zombie;
use pocketmine\scheduler\Task;

final class ClearLagTask extends Task {

    /** @var int */
    public static int $time;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core, int $time = 300) {
        self::$time = $time;
        $this->core = $core;
    }

    public function onRun(): void {
        $time = self::$time;

        if($time === 60 or $time === 30 or $time === 15 or $time === 10 or $time === 5) {
            foreach ($this->core->getServer()->getOnlinePlayers() as $player) {
                $settings = new SettingsManager($player);
                if ($settings->getSettings(SettingsNameIds::CLEARLAG) === false)
                    $player->sendPopup("§cUn clearlag aura lieu dans §e" . $time . " secondes §c!");
            }
        }

        if ($time === 0){

            $count = 0;
            foreach ($this->core->getServer()->getWorldManager()->getWorlds() as $world){
                foreach ($world->getEntities() as $entity){
                    if ($entity instanceof Human)continue;
                    if ($entity instanceof ItemEntity || $entity instanceof ExperienceOrb || $entity instanceof  Zombie){
                        $entity->flagForDespawn();
                        $count++;
                    }
                }
                foreach ($this->core->getServer()->getOnlinePlayers() as $player){
                    $settings = new SettingsManager($player);
                    if ($settings->getSettings(SettingsNameIds::CLEARLAG) === false)
                        $player->sendPopup("§cIl y a eu §e" . $count . " entité(s) §csupprimé(s) !");
                }
            }
            self::$time = 300;
        }
        self::$time--;
    }
}