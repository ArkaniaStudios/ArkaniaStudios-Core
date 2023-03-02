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
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class XpBottleCommand extends BaseCommand {
    use Date;

    /** @var array */
    private array $cooldown = [];

    public function __construct() {
        parent::__construct('xpbottle',
        'XpBottle - ArkaniaStudios',
        '/xpbottle');
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

        if (!isset($this->cooldown[$player->getName()]) || $this->cooldown[$player->getName()] - time() <= 0){
            if ($player->getXpManager()->getXpLevel() <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'xp pour le mettre en bouteille.");
                return true;
            }

            if ($player->hasPermission('arkania:permission.seigneur'))
                $time = 3600;
            elseif($player->hasPermission('arkania:permission.hero'))
                $time = 3600*6;
            elseif($player->hasPermission('arkania:permission.noble'))
                $time = 3600*12;
            else
                $time = 86400;

            $item = VanillaItems::EXPERIENCE_BOTTLE();
            if (!$player->getInventory()->canAddItem($item)){
                $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet, si vous souhaitez exécuter cette commande merci de vider une case de votre inventaire.");
                return true;
            }
            $xpLevel = $player->getXpManager()->getXpLevel();
            $item->setCustomName("§fBouteille d'xp (§e' . $xpLevel . '§f)");
            $player->getInventory()->addItem($item);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez mis §e" . $xpLevel . "§f en bouteille.");
            $this->cooldown[$player->getName()] = $time + time();
        }else {
            $time = $this->tempsFormat($this->cooldown[$player->getName()]);
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pourrez exécuter cette commande dans seulement §e" . $time . "§c.");
        }
        return true;
    }

}