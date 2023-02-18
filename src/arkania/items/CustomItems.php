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

namespace arkania\items;

use arkania\libs\customies\item\CustomiesItemFactory;
use arkania\libs\customies\item\ItemIds;
use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;

final class CustomItems {

    use CloningRegistryTrait;

    public function __construct() {
    }

    /**
     * @return Item[]
     * @phpstan-return array<string, item>
     */
    public static function getAll(): array {
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    /**
     * @param string $id
     * @param Item $item
     * @return void
     */
    protected static function register(string $id, Item $item): void{
        self::_registryRegister($id, $item);
    }

    protected static function setup(): void {
        $factory = CustomiesItemFactory::getInstance();
        self::register(ItemIds::NPC, $factory->get(ItemIds::NPC));
    }

}