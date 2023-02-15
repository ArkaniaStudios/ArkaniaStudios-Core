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

namespace arkania\events\players;

use arkania\Core;
use arkania\manager\FactionManager;
use pocketmine\event\Listener;

class PlayerJoinEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerJoin(\pocketmine\event\player\PlayerJoinEvent $event){
        $player = $event->getPlayer();

        /* PlayerBefore */
        if (!$player->hasPlayedBefore()){
            $jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            $mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
            $num_jour = date('w');
            $jour = $jours[$num_jour];
            $num_mois = date('n') - 1;
            $mois = $mois[$num_mois];
            $annee = date('Y');

            $inscription = $jour . ' ' . date('d') . ' ' . $mois . ' ' . $annee;

            $this->core->stats->setInscription($player, $inscription);
        }

        /* PlayerTime */
        $this->core->stats->createTime($player);

        /* Ranks */
        $this->core->ranksManager->register($player);

        if (!$this->core->ranksManager->existPlayer($player->getName()))
            $this->core->ranksManager->setRank($player->getName(), 'Joueur');
    }
}