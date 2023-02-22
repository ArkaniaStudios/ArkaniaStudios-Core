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
use arkania\tasks\BanTask;
use arkania\utils\Date;
use arkania\utils\Utils;
use pocketmine\event\Listener;

class PlayerJoinEvent implements Listener {

    use Date;

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

        /* Bannissement */
        if ($this->core->sanction->isBan($player->getName())){
            $time = $this->core->sanction->getBanTime($player->getName());
            if ($time - time() <= 0)
                $this->core->sanction->removeBan($player->getName());
            else
                $this->core->getScheduler()->scheduleRepeatingTask(new BanTask($this->core, $player), 7);
        }

        /* PlayerTime */
        $this->core->stats->createTime($player);

        /* Ranks */
        if (!$this->core->ranksManager->existPlayer($player->getName()))
            $this->core->ranksManager->setRank($player->getName(), 'Joueur');

        $this->core->ranksManager->register($player);

        /* Economy */
        if (!$this->core->economyManager->hasAccount($player->getName()))
            $this->core->economyManager->resetMoney($player->getName());


        /* PlayerBefore */
        if (!$player->hasPlayedBefore()){
            $inscription = $this->dateFormat();
            $this->core->stats->setInscription($player, $inscription);
            if (!$this->core->ranksManager->existPlayer($player->getName()))
                $this->core->stats->addPlayerCount();

            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $player->getName() . "§f vient de rejoindre §cArkania §fpour la première fois ! (§7§o#" . $this->core->stats->getPlayerRegister() . "§f)");
            $event->setJoinMessage('');
        }else{
            $event->setJoinMessage('[§a+§f] ' . RanksManager::getRanksFormatPlayer($player));
        }
    }
}