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
use arkania\Core;
use arkania\manager\FactionManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;
use function PHPUnit\Framework\returnArgument;

class FactionCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    /** @var FactionManager */
    private FactionManager $factionManager;

    public function __construct(Core $core) {
        parent::__construct('faction',
        'Faction - ArkaniaStudios',
        '/faction <argument>',
        ['f']);
        $this->core = $core;
        $this->factionManager = new FactionManager();
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();


        $factionManager = $this->factionManager;

        if ($args[0] === 'create'){

            if ($factionManager->getFaction($player->getName()) !== '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes déjà dans un faction. Merci de quitter votre via la commande /f leave afin de pouvoir en créer un nouvelle.");
                return true;
            }

            $this->core->ui->sendCreateFactionForm($player);
        }elseif($args[0] === 'disband'){

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'êtes pas le chef de votre faction. Vous ne pouvez donc pas supprimer cette faction. Si vous voulez créer votre propre faction, faites /f create.");
                return true;
            }

            self::sendToastPacket($player, '§7-> §fFACTION', "§cVOUS VENEZ DE SUPPRIMER LA FACTION " . $factionManager->getFaction($player->getName()) . " !");
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->disbandFaction();
        }
        return true;
    }
}