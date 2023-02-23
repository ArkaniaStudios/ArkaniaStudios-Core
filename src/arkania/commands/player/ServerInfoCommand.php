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

namespace arkania\commands\player;

use arkania\Core;
use arkania\data\WebhookData;
use arkania\exception\QueryException;
use arkania\libs\query\PMQuery;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use arkania\commands\BaseCommand;

class ServerInfoCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('serverinfo',
        'Serverinfo - ArkaniaStudios',
        '/serverinfo');
        $this->core = $core;
    }

    /**
     * @throws QueryException
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (count($args) !== 0)
            return throw new InvalidCommandSyntaxException();

        $tick = $this->core->getServer()->getTicksPerSecond();
        if ($tick >= 16)
            $tps = '§a' . $tick . ' TPS';
        elseif($tick >= 10)
            $tps = '§6' . $tick . ' TPS';
        elseif($tick >= 5)
            $tps = '§c' . $tick . ' TPS';
        else {
            $tps = '§4' . $tick . ' TPS';
            Utils::sendDiscordWebhook('**TPS**', 'Le serveur vient de passer en dessous de **5 TPS** !', 'Server système - ArkaniaStudios', 0xE805B4, WebhookData::ADMIN_LOGS);

        }

        if ($this->core->serverStatus->getServerStatus('Theta') === '§aOuvert')
            $playerTheta = (int)PMQuery::query('arkaniastudios.org', 10297)['Players'];
        else
            $playerTheta = 0;
        if ($this->core->serverStatus->getServerStatus('Zeta') === '§aOuvert')
            $playerZeta = (int)PMQuery::query('arkaniastudios.org', 10298)['Players'];
        else
            $playerZeta = 0;
        if ($this->core->serverStatus->getServerStatus('Epsilon') === '§aOuvert')
            $playerEpsilon = (int)PMQuery::query('arkaniastudios.org', 10299)['Players'];
        else
            $playerEpsilon = 0;

        $allPlayer = $playerTheta + $playerZeta + $playerEpsilon;

        $player->sendMessage(Utils::getPrefix() . "Voici les informations du serveur §e" . Utils::getServerName() . "§f:\n\n§7» §fTPS: " . $tps . "\n§7» §rJoueur(s) en ligne: §e" . count($this->core->getServer()->getOnlinePlayers()) . "\n\n§7» §rJoueurs inscrit: §e" . $this->core->stats->getPlayerRegister() . "\n§7» §rJoueur(s) network: §e" . $allPlayer . "\n\n§7» §fTheta§f: " . $this->core->serverStatus->getServerStatus('Theta') . "\n§7» §fZeta§f: " . $this->core->serverStatus->getServerStatus('Zeta') . "\n§7» §fEpsilon§f: " . $this->core->serverStatus->getServerStatus('Epsilon'));
        return true;
    }

}