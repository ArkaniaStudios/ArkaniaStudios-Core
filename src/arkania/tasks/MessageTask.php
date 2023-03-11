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

namespace arkania\tasks;

use arkania\Core;
use arkania\utils\Utils;
use pocketmine\scheduler\Task;

final class MessageTask extends Task {

    /** @var array */
    private array $message = [
        'Nous recrutons du staff, vous postuler rejoignez notre discord (§e/discord§f) !',
        'N\'hésitez pas à voter pour le serveur via le site (§ehttps://arkaniastudios.org/vote§f)',
        'Si vous avez des questions, rejoignez notre wiki (§ehttps://wiki.arkaniastudios.com/wiki§f)'
    ];

    /** @var int */
    private int $count = 0;

    /** @var Core */
    private Core $core;


    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @return void
     */
    public function onRun(): void {
        $this->core->getServer()->broadcastMessage(Utils::getPrefix() . $this->message[$this->count]);
        $this->count++;
        if ($this->count > count($this->message) - 1)$this->count = 0;
    }
}