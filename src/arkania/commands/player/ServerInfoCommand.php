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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\data\WebhookData;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class ServerInfoCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('serverinfo',
        'Serverinfo - ArkaniaStudios',
        '/serverinfo');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
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
    $this->sendDiscordWebhook('**TPS**', "Le serveur **" . Utils::getServerName() . "** vient de passer en dessous de **5 TPS** !" . PHP_EOL . PHP_EOL . "- Nombre de joueur connecté **" . count($this->core->getServer()->getOnlinePlayers()) . "**", 'Server système - ArkaniaStudios', 0xE805B4, WebhookData::TPS);
    }

    if ($this->core->getServerStatus()->getServerStatus('Theta') === '§aOuvert')
    $playerTheta = '§cSoon...';
    else
    $playerTheta = 0;
    if ($this->core->getServerStatus()->getServerStatus('Zeta') === '§aOuvert')
    $playerZeta = '§cSoon...';
    else
    $playerZeta = 0;
    if ($this->core->getServerStatus()->getServerStatus('Epsilon') === '§aOuvert')
    $playerEpsilon = '§cSoon...';
    else
    $playerEpsilon = 0;

    $allPlayer = $playerTheta + $playerZeta + $playerEpsilon;

    $player->sendMessage(Utils::getPrefix() . "Voici les informations du serveur §e" . Utils::getServerName() . "§f:\n\n§7» §fTPS: " . $tps . "\n§7» §rJoueur(s) en ligne: §e" . count($this->core->getServer()->getOnlinePlayers()) . "\n\n§7» §rJoueurs inscrit: §e" . $this->core->getStatsManager()->getPlayerRegister() . "\n§7» §rJoueur(s) network: §e" . $allPlayer . "\n\n§7» §fTheta§f: " . $this->core->getServerStatus()->getServerStatus('Theta') . "\n§7» §fZeta§f: " . $this->core->getServerStatus()->getServerStatus('Zeta') . "\n§7» §fEpsilon§f: " . $this->core->getServerStatus()->getServerStatus('Epsilon'));
    return true;
    }
    }

