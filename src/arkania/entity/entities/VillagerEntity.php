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

namespace arkania\entity\entities;

use arkania\entity\base\BaseEntity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VillagerEntity extends BaseEntity {

    /**
     * @return string
     */
    public function getName(): string {
        return 'Villager';
    }

    /**
     * @return string
     */
    public static function getNetworkTypeId(): string {
        return EntityIds::VILLAGER;
    }

    /** @var float  */
    private float $height = 1.5;

    /** @var float  */
    private float $width = 0.9;

    /**
     * @return EntitySizeInfo
     */
    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }
}