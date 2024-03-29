<?php

namespace arkania\entities\base;

use arkania\Core;
use arkania\entities\EntityDataIds;
use arkania\entities\EntityTrait;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class CustomEntity extends Human {
    use EntityTrait;

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null) {
        parent::__construct($location, $skin, $nbt);
        if(!is_null($nbt) && $nbt->getTag(EntityDataIds::ENTITY_NPC) !== null){
            $this->restorEntityData($nbt);
            $this->setScale($this->getTaille());
        }
        $this->setNameTagAlwaysVisible();
    }

    public function saveNBT() : CompoundTag {
        $nbt = parent::saveNBT();
        if($this->isNpc()){
            $nbt = $this->saveEntityData($nbt);
        }
        return $nbt;
    }

    public function attack(EntityDamageEvent $source) : void {
        if(!$this->isNpc){
            parent::attack($source);
        }elseif($source instanceof EntityDamageByEntityEvent){
            $player = $source->getDamager();
            $entity = $source->getEntity();
            if($player instanceof Player) {
                if($entity instanceof CustomEntity){
                    if($player->getInventory()->getItemInHand()->getId() === 507) {
                        Core::getInstance()->getEntityFormManager()->sendEntityItemForm($player, $source->getEntity());
                    }else{
                        $this->executeCommand($player);
                    }
                }
            }
        }
    }
}