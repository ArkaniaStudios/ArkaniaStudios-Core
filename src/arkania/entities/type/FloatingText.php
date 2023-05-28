<?php

namespace arkania\entities\type;

use arkania\entities\base\SimpleEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FloatingText extends SimpleEntity
{
    public $gravity = 0;

    protected function getInitialSizeInfo() : EntitySizeInfo
    {
        return new EntitySizeInfo('0.1', '0.1');
    }
    public static function getNetworkTypeId() : string
    {
        return EntityIds::FALLING_BLOCK;
    }
    public function getName() : string
    {
        return 'floating text';
    }
}