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

namespace arkania\commands\admin\ranks;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

final class AddRankCommand extends BaseCommand {

    /** @var Core  */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('addrank',
        'Addrank - ArkaniaStudios',
        '/addrank <rankName>');
        $this->setPermission('arkania:permission.addrank');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if (!Utils::isValidArgument($args[0])) {
            $player->sendMessage(Utils::getPrefix() . "§cCe nom n'est pas un nom valide, merci de ne mettre que des caractères compris entre : a & z, A & Z et 0 & 9.");
            return true;
        }

        if ($this->core->getRanksManager()->existRank($args[0])){
            $player->sendMessage(Utils::getPrefix() . "§cCe grade existe déjà.");
            return true;
        }

        $this->core->getRanksManager()->addRank($args[0]);
        $player->sendMessage(Utils::getPrefix() . "§aVous venez d'ajouter le grade §2" . $args[0] . "§f.");
        $this->sendStaffLogs($player->getName() . ' vient de créer le grade ' . $args[0] . '§f.');
        return true;
    }
}