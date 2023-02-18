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

namespace arkania\libs\customies\task;

use arkania\libs\customies\block\CustomiesBlockFactory;
use pocketmine\block\Block;
use pocketmine\scheduler\AsyncTask;
use ReflectionException;

final class AsyncRegisterBlocksTask extends AsyncTask {

    public function __construct(private string $blocks) {
    }

    /**
     * @throws ReflectionException
     */
    public function onRun(): void {
        /** @phpstan-var array<string, Block> $blocks */
        $blocks = unserialize($this->blocks);
        foreach($blocks as $identifier => $block){
            /** @phpstan-var class-string $className */
            $className = get_class($block);
            CustomiesBlockFactory::getInstance()->registerBlock($className, $identifier, $block->getName(), $block->getBreakInfo());
        }
        CustomiesBlockFactory::getInstance()->registerCustomRuntimeMappings();
    }

}