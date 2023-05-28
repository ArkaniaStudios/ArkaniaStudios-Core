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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;

final class RepairCommand extends BaseCommand {
    use Date;

    /** @var array */
    private array $cooldown = [];

    public function __construct() {
        parent::__construct('repair',
        'Permet de réparer un objet.',
        '/repair <all/hand>');
        $this->setPermission('arkania:permission.repair');
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

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if ($player->hasPermission('arkania:permission.seigneur'))
            $time = 300;
        elseif($player->hasPermission('arkania:permission.hero'))
            $time = 420;
        elseif($player->hasPermission('arkania:permission.noble'))
            $time = 540;
        else
            $time = 600;

        if (!isset($this->cooldown[$player->getName()]) || $this->cooldown[$player->getName()] - time() <= 0) {
            if (strtolower($args[0]) === 'hand') {

                $itemInHand = $player->getInventory()->getItemInHand();
                if (!($itemInHand instanceof Tool or $itemInHand instanceof Armor)){
                    $player->sendMessage(Utils::getPrefix() . "§cCet objet ne peut pas être réparé.");
                    return true;
                }

                $slot = $player->getInventory()->getHeldItemIndex();
                $item = $player->getInventory()->getItem($slot);
                if ($item->getMeta() <= 0){
                    $player->sendMessage(Utils::getPrefix() . "§cCet objet n'a pas besoin d'être réparé.");
                    return true;
                }
                $player->getInventory()->setItem($slot, $item->setDamage(0));
                $player->sendMessage(Utils::getPrefix() . "§aVotre objet a bien été réparé.");
                $this->cooldown[$player->getName()] = $time + time();
            }elseif (strtolower($args[0]) === 'all') {
                foreach ($player->getInventory()->getContents() as $slot => $item){
                    if ($item instanceof Tool or $item instanceof Armor){
                        if ($item->getMeta() > 0) {
                            $player->getInventory()->setItem($slot, $item->setDamage(0));
                        }
                    }
                }

                foreach ($player->getArmorInventory()->getContents() as $slot => $item){
                    if ($item instanceof Armor){
                        if ($item->getMeta() > 0) {
                            $player->getArmorInventory()->setItem($slot, $item->setDamage(0));
                        }
                    }
                }
                $player->sendMessage(Utils::getPrefix() . "§aTous vos objets ont été réparé.");
                $this->cooldown[$player->getName()] = $time*27;
            } else
                return throw new InvalidCommandSyntaxException();
        }else
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez utilisé cette commande que dans §e" . $this->tempsformat($this->cooldown[$player->getName()]));
        return true;
    }
}