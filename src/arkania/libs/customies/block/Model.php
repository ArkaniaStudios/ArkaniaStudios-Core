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

namespace arkania\libs\customies\block;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

final class Model {

    /** @var Material[] */
    private array $materials;
    private string $geometry;
    private Vector3 $origin;
    private Vector3 $size;

    /**
     * @param Material[] $materials
     */
    public function __construct(array $materials, string $geometry, Vector3 $origin, Vector3 $size) {
        $this->materials = $materials;
        $this->geometry = $geometry;
        $this->origin = $origin;
        $this->size = $size;
    }

    /**
     * Returns the model in the correct NBT format supported by the client.
     * @return CompoundTag[]
     */
    public function toNBT(): array {
        $materials = CompoundTag::create();
        foreach($this->materials as $material){
            $materials->setTag($material->getTarget(), $material->toNBT());
        }

        return [
            "minecraft:material_instances" => CompoundTag::create()
                ->setTag("mappings", CompoundTag::create()) // What is this? The client will crash if it is not sent.
                ->setTag("materials", $materials),
            "minecraft:geometry" => CompoundTag::create()
                ->setString("value", $this->geometry),
            "minecraft:pick_collision" => CompoundTag::create()
                ->setByte("enabled", 1)
                ->setTag("origin", new ListTag([
                    new FloatTag($this->origin->getX()),
                    new FloatTag($this->origin->getY()),
                    new FloatTag($this->origin->getZ())
                ]))
                ->setTag("size", new ListTag([
                    new FloatTag($this->size->getX()),
                    new FloatTag($this->size->getY()),
                    new FloatTag($this->size->getZ())
                ]))
        ];
    }
}