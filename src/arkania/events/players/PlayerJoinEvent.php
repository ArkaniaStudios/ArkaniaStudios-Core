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
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\event\Listener;

class PlayerJoinEvent implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    public function onPlayerJoin(\pocketmine\event\player\PlayerJoinEvent $event){
        $player = $event->getPlayer();

        /* Maintenance */
        if ($this->core->serverStatus->getServerStatus(Utils::getServerName()) === '§6Maintenance'){
            if (!$player->hasPermission('arkania:permission.maintenance.bypass')){
                $player->sendMessage(Utils::getPrefix() . "§cLe serveur est actuellement en maintenance. Merci de rester patient.");
                $player->transfer('lobby1');
            }
        }

        /* PlayerTime */
        $this->core->stats->createTime($player);

        /* Ranks */
        $this->core->ranksManager->register($player);

        if (!$this->core->ranksManager->existPlayer($player->getName()))
            $this->core->ranksManager->setRank($player->getName(), 'Joueur');

        /* Economy */
        if (!$this->core->economyManager->hasAccount($player->getName()))
            $this->core->economyManager->resetMoney($player->getName());


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
            $this->core->stats->addPlayerCount();

            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $player->getName() . "§f vient de rejoindre §cArkania §fpour la première fois ! (§7§o#" . $this->core->stats->getPlayerRegister() . "§f)");
            $event->setJoinMessage('');
        }else{
            $event->setJoinMessage('[§a+§f] ' . RanksManager::getRanksFormatPlayer($player));
        }
    }
}