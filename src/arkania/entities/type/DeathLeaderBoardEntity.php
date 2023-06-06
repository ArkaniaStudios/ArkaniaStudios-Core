<?php

namespace arkania\entities\type;

use arkania\Core;
use arkania\entities\base\SimpleEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DeathLeaderBoardEntity extends SimpleEntity
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
        return 'DeathLeaderBoard';
    }

    public function onUpdate(int $currentTick) : bool
    {
        if(!isset($this->cooldown[$this->getName()]) or $this->cooldown[$this->getName()] - time() <= 0) {
            $res = Core::getInstance()->getStatsManager()->getAllDeath();
            arsort($res);
            $top = 1;
            $nametag = "§c- §fListe des joueurs avec le plus de morts §c-\n";
            foreach ($res as $name => $morts) {
                if($top === 11)
                    break;
                $nametag .= "§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $morts . "\n";
                $top++;
            }
            $this->setNameTag($nametag);
            $this->cooldown[$this->getName()] = time() + 60;
        }
        return parent::onUpdate($currentTick);
    }
}