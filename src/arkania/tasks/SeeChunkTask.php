<?php
declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _             __     __  ____
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \            \ \   / / |___ \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \    _____   \ \ / /    __) |
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \  |_____|   \ V /    / __/
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\            \_/    |_____|
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 */

namespace arkania\tasks;

use arkania\commands\player\FactionCommand;
use arkania\Core;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\format\Chunk;
use pocketmine\world\particle\RedstoneParticle;

class SeeChunkTask extends Task {

    public function onRun(): void
    {
        foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $p) {
            if (isset(FactionCommand::$chunk[$p->getName()])) {
                $chunkX = $p->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE;
                $chunkZ = $p->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE;

                $minX = (float)$chunkX * 16;
                $maxX = (float)$minX + 16;
                $minZ = (float)$chunkZ * 16;
                $maxZ = (float)$minZ + 16;

                for ($x = $minX; $x <= $maxX; $x += 0.5) {
                    for ($z = $minZ; $z <= $maxZ; $z += 0.5) {
                        if ($x === $minX || $x === $maxX || $z === $minZ || $z === $maxZ) {
                            $p->getWorld()->addParticle(new Vector3($x, $p->getPosition()->y + 1.5, $z), new RedstoneParticle(), [$p]);
                        }
                    }
                }
            }
        }
    }

}