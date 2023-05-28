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

namespace arkania\manager;

use arkania\Core;
use arkania\utils\Utils;

final class MaintenanceManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param bool $key
     * @return void
     */
    public function setMaintenance(bool $key = true): void {

        $serverStatus = new ServerStatusManager();
        if ($key === true) {
            $serverStatus->setServerStatus('maintenance');

            foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer) {

                $onlinePlayer->sendMessage(Utils::getPrefix() . "§aLe serveur §e" . Utils::getServerName() . "§a vient de passer en maintenance.");
                if (!$onlinePlayer->hasPermission('arkania:permission.maintenance.bypass') || !$onlinePlayer->getServer()->isOp($onlinePlayer->getName())){
                    $onlinePlayer->transfer('lobby1');
                }
            }
        }else{
            $serverStatus->setServerStatus('ouvert');
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§aLa maintenance est maintenant terminé !");
        }
    }
}