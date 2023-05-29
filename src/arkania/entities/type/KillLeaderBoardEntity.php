<?php

namespace arkania\entities\type;

use arkania\Core;
use arkania\entities\base\SimpleEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class KillLeaderBoardEntity extends SimpleEntity
{
    private array $cooldown = [];

    protected function getInitialSizeInfo() : EntitySizeInfo
    {
        return new EntitySizeInfo(1, 1);
    }
    public static function getNetworkTypeId() : string
    {
        return EntityIds::FALLING_BLOCK;
    }
    public function getName() : string
    {
        return 'KillLeaderBoard';
    }

    public function onUpdate(int $currentTick) : bool
    {
        if(!isset($this->cooldown[$this->getName()]) or $this->cooldown[$this->getName()] - time() <= 0) {
            $res = Core::getInstance()->getStatsManager()->getAllKill();
            arsort($res);
            $top = 1;
            $nametag = "§c- §fListe des joueurs avec le plus de kills §c-\n";
            foreach ($res as $name => $kills) {
                if($top === 11)
                    break;
                $nametag .= "§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $kills . "\n";
                $top++;
            }
            $this->setNameTag($nametag);
            $this->cooldown[$this->getName()] = time() + 60;
        }
        return parent::onUpdate($currentTick);
    }
}