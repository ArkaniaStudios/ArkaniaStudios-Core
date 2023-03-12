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

namespace arkania\utils;

use arkania\tasks\async\QueryTask;
use pocketmine\Server;

class Query {

    /**
     * @param string $text
     * @param callable|null $completion
     * @return void
     */
    public static function query(string $text, callable $completion = null): void{
        Server::getInstance()->getAsyncPool()->submitTask(new QueryTask($text, $completion));
    }
}