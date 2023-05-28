<?php

namespace arkania\entities\type;

use arkania\entities\base\SimpleEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VillagerEntity extends SimpleEntity
{

    protected function getInitialSizeInfo() : EntitySizeInfo
    {
        return new EntitySizeInfo(1.9, 0.6);
    }
    public static function getNetworkTypeId() : string
    {
        return EntityIds::VILLAGER;
    }
    public function getName() : string
    {
        return 'Villageois';
    }
}