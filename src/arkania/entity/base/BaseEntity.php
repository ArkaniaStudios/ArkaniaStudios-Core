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

namespace arkania\entity\base;

use arkania\Core;
use arkania\entity\EntityIds;
use arkania\entity\EntityTrait;
use arkania\items\npc\NpcManagerItem;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

abstract class BaseEntity extends Living {

    use EntityTrait;

    public function __construct(Location $location, ?CompoundTag $nbt = null) {
        parent::__construct($location, $nbt);
        if (!is_null($nbt) && $nbt->getTag(EntityIds::NPC) !== null)
            $this->restorNpcData($nbt);
    }

    public function saveNBT(): CompoundTag {

        $data = parent::saveNBT();
        if ($this->isNpc())
            $this->saveNpcData($data);
        return $data;
    }

    public function attack(EntityDamageEvent $source): void {
        if ($source instanceof EntityDamageByEntityEvent) {
            $player = $source->getDamager();
            if ($player instanceof Player) {
                if ($player->getInventory()->getItemInHand()->getId() === VanillaItems::RECORD_STRAD()->getId())
                    Core::getInstance()->ui->sendMenuForm($player, $this);
                else
                    $this->executeCommand($player);
            }
        }
    }
}