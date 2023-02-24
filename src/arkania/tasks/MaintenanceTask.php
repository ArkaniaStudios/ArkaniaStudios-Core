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

namespace arkania\tasks;

use arkania\Core;
use arkania\data\WebhookData;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class MaintenanceTask extends Task {
    use Webhook;

    /** @var Core */
    private Core $core;

    /** @var int */
    private int $time =20;

    /** @var string */
    private string $ranks;

    public function __construct(Core $core, string $ranks) {
        $this->core = $core;
        $this->ranks = $ranks;
    }

    public function onRun(): void {
        if ($this->time === 0) {
            $this->sendDiscordWebhook('**MAINTENANCE**', "La maintenance vient d'être activé sur un serveur." . PHP_EOL . PHP_EOL . "- Server : **" . Utils::getServerName() . "**" . PHP_EOL . "- Staff : " . Utils::removeColorOnMessage($this->ranks), 'Maintenance Système - ArkaniaStudios', 0xEFA, WebhookData::MAINTENANCE);
            $this->core->maintenance->setMaintenance();
        }elseif($this->time === 2) {
            foreach (Server::getInstance()->getOnlinePlayers() as $players)
                $players->removeCurrentWindow();
        }elseif($this->time === 10)
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§aLe serveur va passer en maintenance dans §e" . $this->time . " secondes§a !");
        elseif ($this->time === 20)
            $this->core->getServer()->broadcastMessage(Utils::getPrefix() . "§aLe serveur va passer en maintenance dans §e" . $this->time . " secondes§a !");

        $this->time--;
    }
}