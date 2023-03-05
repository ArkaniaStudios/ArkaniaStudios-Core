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

namespace arkania\tasks\async;

use pocketmine\scheduler\AsyncTask;

class VoteTask extends AsyncTask {

    /** @var callable */
    private $callable1;

    /** @var callable */
    private $callable2;
    public function __construct(callable $callable1, callable $callable2) {
        $this->callable1 = $callable1;
        $this->callable2 = $callable2;
    }

    /**
     * @return void
     */
    public function onRun(): void {
        call_user_func($this->callable1, $this);
    }

    /**
     * @return void
     */
    public function onCompletion(): void {
         call_user_func($this->callable2, $this);
    }

}