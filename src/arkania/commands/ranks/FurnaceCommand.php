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

namespace arkania\commands\ranks;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\crafting\FurnaceType;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

final class FurnaceCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('furnace',
        'Furnace - ArkaniaStudios',
        '/furnace <all/hand>');
        $this->setPermission('arkania:permission.furnace');
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

        $item = $player->getInventory()->getItemInHand();
        $number = $item->getCount();

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        if(strtolower($args[0]) === 'hand') {

            if ($player->getInventory()->getItemInHand() === null) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas faire cuire cet objet.");
                return true;
            }

            if($item->getId() == 363) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(263)->setCount($number));
                $this->extracted($number, $player);
            }elseif($item->getId() == 319) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(320)->setCount($number));
                $this->extracted($number, $player);
            } else if($item->getId() == 365) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(424));
                $this->extracted($number, $player);
            } else if($item->getId() == 349) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(350));
                $this->extracted($number, $player);
            } else if($item->getId() == 349) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(424));
                $this->extracted($number, $player);
            } else if($item->getId() == 411) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(412));
                $this->extracted($number, $player);
            } else if($item->getId() == 460) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(463));
                $this->extracted($number, $player);
            } else if($item->getId() == 392) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(393));
                $this->extracted($number, $player);
            } else if($item->getId() == 15) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(265));
                $this->extracted($number, $player);
            } else if($item->getId() == 153) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(266));
                $this->extracted($number, $player);
            } else if($item->getId() == 12) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(20));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 4) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(1));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 337) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(336));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 87) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(405));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 82) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(172));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 81) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(351, 2));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 17) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(263, 1));
                $player->getXpManager()->addXp($number);
            } else if($item->getId() == 56) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(264));
                $this->extracted($number, $player);
            } else if($item->getId() == 73) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(331));
                $this->extracted($number, $player);
            } else if($item->getId() == 16) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(263));
                $this->extracted($number, $player);
            } else if($item->getId() == 129) {
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(388));
                $this->extracted($number, $player);
            } else
                $player->sendMessage(Utils::getPrefix() . "§cL'objet §c(§e" . $item->getName() . "§c) ne peut pas être cuit !");
        }if($args[0] === "all") {
            for ($i = 0; $i < $player->getInventory()->getSize(); ++$i) {
                $result = $this->getFurnaceResult($player->getInventory()->getItem($i));
                if ($result != null) {
                    $player->getInventory()->setItem($i, $result);
                }
            }
            $player->sendMessage(Utils::getPrefix() . "§aTous les objets ont été cuits");
        }
        return true;
    }

    /**
     * @param int $number
     * @param CommandSender|Player $sender
     * @return void
     */
    public function extracted(int $number, CommandSender|Player $sender): void
    {
        if ($number > 0 && $number < 11) {
            $sender->getXpManager()->addXp($number);
        } else if ($number > 10 && $number < 21) {
            $sender->getXpManager()->addXp($number);
        } else if ($number > 20 && $number < 31) {
            $sender->getXpManager()->addXp($number);
        } else if ($number > 30 && $number < 41) {
            $sender->getXpManager()->addXp($number);
        } else if ($number > 40 && $number < 51) {
            $sender->getXpManager()->addXp($number);
        } else if ($number > 50 && $number <= 64) {
            $sender->getXpManager()->addXp($number);
        } else {
            $sender->getXpManager()->addXp($number);
        }
    }

    /**
     * @param Item $item
     * @return Item|null
     */
    private function getFurnaceResult(Item $item) : ?Item {
        $recipe = Core::getInstance()->getServer()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE())->match($item);
        if($recipe != null && $item->getId() != 337) {
            return $recipe->getResult()->setCount($item->getCount());
        }
        return null;
    }
}