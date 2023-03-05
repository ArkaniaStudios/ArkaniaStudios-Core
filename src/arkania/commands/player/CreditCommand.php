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
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

final class CreditCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('credit',
        'Credit - ArkaniaStudios',
        '/credit');
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        $player->sendMessage(Utils::getPrefix() . "Voici la liste des personnes ayant travaillé(e)s sur le serveur :\n\n§6Pôle Administratif §f:\n§f- §4TEZULS §7-> §ehttps://github.com/tezuls\n§f- §cWolfyDiams §7-> §eWolfy_Diams#5227\n§f- §6Altenny §7-> §eAltenny#5017\n§f- §6DeXking §7-> §eDeXking#8518§f\n- §6Rorocry §7-> §eRoro#8151\n\n§f§2Pôle Développement §f:\n§f- §2geotre §7-> §ehttps://github.com/geotre223\n§f- §2Julien §7-> §ehttps://github.com/Dumont-Julien§f.\n\n§7» §rMerci à toutes ses personnes d'avoir travaillé(e) sur arkania, en espérant que le serveur vous conviennes.");
        return true;
    }

}