<?php
declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _                   _      ____    ___
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \                 / \    |  _ \  |_ _|
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \    _____      / _ \   | |_) |  | |
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \  |_____|    / ___ \  |  __/   | |
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\           /_/   \_\ |_|     |___|
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 */

namespace arkania\entity\entities;

use arkania\Core;
use arkania\entity\base\BaseEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MoneyLeaderBoard extends BaseEntity {

    /** @var float */
    protected $gravity = 0.0;

    /**
     * @return EntitySizeInfo
     */
    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.6, 0.5);
    }

    /**
     * @return string
     */
    public static function getNetworkTypeId(): string
    {
        return EntityIds::CHICKEN;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'money';
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool
    {
        $res = Core::getInstance()->getEconomyManager()->getAllMoney();
        $ret = [];
        foreach ($res as $val) {
            $ret[$val[0]] = $val[1];
        }
        arsort($ret);
        $top = 1;
        $nametag = "§c- §fListe des joueurs avec le plus d'argent §c-\n";
        foreach ($ret as $name => $money) {
            if ($top === 11) break;
            $nametag .= "§4#" . $top . " §l§7» §r§c" . $name . " §favec §c" . $money . "\n";
            $top++;
        }
        $this->setNameTag($nametag);
        return parent::onUpdate($currentTick);
    }
}