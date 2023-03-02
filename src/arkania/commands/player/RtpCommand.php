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

use arkania\commands\BaseCommand;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class RtpCommand extends BaseCommand {
    use Date;

    /** @var array */
    private array $rtp;

    public function __construct() {
        parent::__construct('rtp',
        'Rtp - ArkaniaStudios',
        '/rtp');
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if ($player->hasPermission('arkania:permission.seigneur'))
            $time = time() + 3600;
        elseif($player->hasPermission('arkania:permission.hero'))
            $time = time() + 3600 * 6;
        elseif($player->hasPermission('arkania:permission.noble'))
            $time = time() + 3600 * 12;
        else
            $time = time() + 86400;

        if (!isset($this->rtp[$player->getName()]) || $this->rtp[$player->getName()] - time() <= 0) {
            $z = 0;
            $x = 0;
            $xv = mt_rand(1, 2);
            if ($xv == 1) $x = mt_rand(-3000, -1500);
            if ($xv == 2) $x = mt_rand(1500, 3000);
            $zv = mt_rand(1, 2);
            if ($zv == 1) $z = mt_rand(-3000, -1500);
            if ($zv == 2) $z = mt_rand(1500, 3000);
            $y = 200;
            $player->teleport(new Vector3($x, $y, $z));
            $this->rtp[$player->getName()] = $time;
        } else
            $player->sendMessage(Utils::getPrefix() . "§cMerci d'attendre encore §e" . $this->tempsFormat($time) . "§c avant de vous téléporter au hasard dans le monde!");
        return true;
    }

}