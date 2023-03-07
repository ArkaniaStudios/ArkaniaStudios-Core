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

namespace arkania\events\players;

use arkania\Core;
use arkania\manager\RanksManager;
use arkania\tasks\BanTask;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\event\Listener;

final class PlayerJoinEvent implements Listener {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param \pocketmine\event\player\PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(\pocketmine\event\player\PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        /* Maintenance */
        if ($this->core->getServerStatus()->getServerStatus(Utils::getServerName()) === '§6Maintenance'){
            if (!$player->hasPermission('arkania:permission.maintenance.bypass')){
                $player->sendMessage(Utils::getPrefix() . "§cLe serveur est actuellement en maintenance. Merci de rester patient.");
                $player->transfer('lobby1');
            }
        }

        /* Bannissement */
        if ($this->core->getSanctionManager()->isBan($player->getName())){
            $time = $this->core->getSanctionManager()->getBanTime($player->getName());
            if ($time - time() <= 0)
                $this->core->getSanctionManager()->removeBan($player->getName());
            else {
                $this->core->getScheduler()->scheduleRepeatingTask(new BanTask($this->core, $player), 7);
                return;
            }
        }

        /* Economy */
        if (!$this->core->getEconomyManager()->hasAccount($player->getName()))
            $this->core->getEconomyManager()->resetMoney($player->getName());

        $this->core->getStatsManager()->setServerConnection($player);

        foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer){
            if ($this->core->getStaffManager()->isInVanish($onlinePlayer))
                if (!$player->hasPermission('arkania:permission.vanish'))
                    $player->hidePlayer($onlinePlayer);
        }

        /* PlayerBefore */
        if (!$player->hasPlayedBefore()){
                $inscription = $this->dateFormat();
                $this->core->getStatsManager()->setInscription($player, $inscription);
                if (!$this->core->getRanksManager()->existPlayer($player->getName()))
                    $this->core->getStatsManager()->addPlayerCount();

                $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§e" . $player->getName() . "§f vient de rejoindre §cArkania §fpour la première fois ! (§7§o#" . $this->core->getStatsManager()->getPlayerRegister() . "§f)");
                $event->setJoinMessage('');
        }else{
            $event->setJoinMessage('');
            $player->sendPopup('[§a+§f] ' . RanksManager::getRanksFormatPlayer($player));
        }
    }
}