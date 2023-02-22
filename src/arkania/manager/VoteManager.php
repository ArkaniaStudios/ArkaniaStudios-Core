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

namespace arkania\manager;

use arkania\Core;
use arkania\utils\Utils;
use JsonException;
use pocketmine\utils\Config;

final class VoteManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function addVoteParty(): void {
        $path = new Config($this->core->getDataFolder() . 'voteparty.json', Config::JSON);
        $path->set('vote-party', $path->get('vote-party') + 1);
        $path->save();
    }

    /**
     * @return void
     */
    public function voteParty(): void {
        if ($this->getCount() >= 100){
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "Le vote party vient d'être atteins ! Un événement vient d'être lancé.");
        }
    }

    /**
     * @return int
     */
    public function getCount(): int {
        $path = new Config($this->core->getDataFolder() . 'voteparty.json', Config::JSON);
        return $path->exists('vote-party') ? $path->get('vote-party') : 0;
    }

}