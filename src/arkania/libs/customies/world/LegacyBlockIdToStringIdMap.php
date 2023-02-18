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

namespace arkania\libs\customies\world;

use pocketmine\utils\SingletonTrait;
use const pocketmine\BEDROCK_DATA_PATH;

final class LegacyBlockIdToStringIdMap {

    use SingletonTrait;

    /**
     * @var string[]
     * @phpstan-var array<int, string>
     */
    private array $legacyToString;
    /**
     * @var int[]
     * @phpstan-var array<string, int>
     */
    private array $stringToLegacy;

    public function __construct() {
        /** @phpstan-var array<string, int> $blockIdMap */
        $blockIdMap = json_decode((string)file_get_contents(BEDROCK_DATA_PATH . "block_id_map.json"), true);
        $this->stringToLegacy = $blockIdMap;
        /** @phpstan-var array<int, string> $flipped */
        $flipped = array_flip($this->stringToLegacy);
        $this->legacyToString = $flipped;
    }

    public function legacyToString(int $legacy): ?string {
        return $this->legacyToString[$legacy] ?? null;
    }

    public function stringToLegacy(string $string): ?int {
        return $this->stringToLegacy[$string] ?? null;
    }

    public function registerMapping(string $string, int $legacy): void {
        $this->legacyToString[$legacy] = $string;
        $this->stringToLegacy[$string] = $legacy;
    }

}