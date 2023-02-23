<?php

declare(strict_types=1);

namespace arkania\events\players;

use arkania\Core;
use pocketmine\event\Listener;

class PlayerLoginEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerLogin(\pocketmine\event\player\PlayerLoginEvent $event) {
        $player = $event->getPlayer();

    }
}