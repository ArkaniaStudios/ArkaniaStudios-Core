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

namespace arkania\manager;

use arkania\Core;
use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

final class ShopManager {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendShopForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data){
            if (is_null($data))
                return;

            if ($data === 0)
                $this->sendBlocsForm($player);

        });
        $form->setTitle('§c- §fShop §c-');
        $form->setContent('§7» §rChoisissez une catégorie.');
        $form->addButton('§7» §rBlocs', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/dirt');
        $form->addButton('§7» §rAgriculture', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/bread');
        $form->addButton('§7» §rMinerais', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/diamond');
        $form->addButton('§7» §rAutres', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/diamond_sword');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    private function sendBlocsForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data){

            if (is_null($data))
                return;

            if ($data === 0)
                $this->sendLogOakForm($player);

        });
        $form->setTitle('§c- §fBlocs §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§f-------------------------------');
        $form->addButton('§7» §rBois de chêne', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_oak');
        $form->addButton('§7» §rBois de bouleau', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_birch');
        $form->addButton('§7» §rBois d\'acacia', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_acacia');
        $form->addButton('§7» §rBois de sapin', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_big_oak');
        $form->addButton('§7» §rBois de bouleau', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_birch');
        $form->addButton('§7» §rPierre', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/stone');
        $form->addButton('§7» §rPierre taillé', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/cobblestone');
        $form->addButton('§7» §cRetour', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/barrier');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendLogOakForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::OAK_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c tronc de chêne.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 4){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c tronc de chêne.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 4);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a tronc de chêne pour un total de §e' . (int)$data[2] * 4 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::OAK_LOG()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::OAK_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c tronc de chêne à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->remove($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a tronc de chêne pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fBois de chêne §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a4/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendLogBirchForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::BIRCH_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c bois de bouleau.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 4){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c bois de bouleau.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 4);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a bois de bouleau pour un total de §e' . (int)$data[2] * 4 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::OAK_LOG()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::OAK_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c bois de bouleau à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->remove($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a bois de bouleau pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fBois de bouleau §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a4/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }


    /**
     * @param Player $player
     * @param int $id
     * @return int
     */
    private function countItem(Player $player, int $id): int {
        $count = 0;
        foreach ($player->getInventory()->getContents() as $item){
            if ($item instanceof Item){

                if ($item->getId() == $id){

                    $count += $item->getCount();

                }
            }
        }
        return $count;
    }
}