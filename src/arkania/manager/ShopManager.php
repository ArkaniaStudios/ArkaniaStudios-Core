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
use pocketmine\item\VanillaItems;
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
            if (is_null($data)) return;

            switch($data){
                case 0:
                    $this->sendBlocsForm($player);
                    break;
                case 1:
                    $this->sendAgricultureForm($player);
                    break;
                case 2:
                    $this->sendOreForm($player);
                    break;
                case 3:
                    $this->sendLootForm($player);
                    break;
                default:
                    $this->sendShopForm($player);
                    break;
            }
        });
        $form->setTitle('§c- §fShop §c-');
        $form->setContent('§7» §rChoisissez une catégorie.');
        $form->addButton('§7» §rBlocs', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/dirt');
        $form->addButton('§7» §rAgriculture', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/bread');
        $form->addButton('§7» §rMinerais', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/diamond');
        $form->addButton('§7» §rLoots', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/bone');
        $form->addButton('§7» §rDivers', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/diamond_sword');
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
            elseif($data === 1)
                $this->sendLogBirchForm($player);
            elseif($data === 2)
                $this->sendLogAcaciaForm($player);
            elseif($data === 3)
                $this->sendLogSpruceForm($player);
            elseif($data === 4)
                $this->sendStoneForm($player);
            elseif($data === 5)
                $this->sendCobblestoneForm($player);
            elseif($data === 6)
                $this->sendDirtForm($player);
            else
                $this->sendShopForm($player);

        });
        $form->setTitle('§c- §fBlocs §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§f-------------------------------');
        $form->addButton('§7» §rBois de chêne', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_oak');
        $form->addButton('§7» §rBois de bouleau', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_birch');
        $form->addButton('§7» §rBois d\'acacia', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_acacia');
        $form->addButton('§7» §rBois de sapin', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/log_big_oak');
        $form->addButton('§7» §rPierre', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/stone');
        $form->addButton('§7» §rPierre taillé', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/cobblestone');
        $form->addButton('§7» §rTerre', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/dirt');
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
                $player->getInventory()->removeItem($itemSell);
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
                $item = $this->countItem($player, VanillaBlocks::BIRCH_LOG()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::BIRCH_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c bois de bouleau à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
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
     * @return void
     */
    public function sendLogAcaciaForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::ACACIA_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c bois d'acacia.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 4){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c bois d'acacia.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 4);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a bois d\'acacia pour un total de §e' . (int)$data[2] * 4 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::ACACIA_LOG()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::ACACIA_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c bois d'acacia à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a bois d'acacia pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fBois d\'acacia §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a4/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendLogSpruceForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::SPRUCE_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c bois de ssapin.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 4){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c bois de sapin.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 4);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a bois de sapin pour un total de §e' . (int)$data[2] * 4 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::SPRUCE_LOG()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::SPRUCE_LOG()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c bois de sapin à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a bois de sapin pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fBois de sapin §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a4/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendStoneForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::STONE()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c pierre.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 5){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c pierre.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 5);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a pierre pour un total de §e' . (int)$data[2] * 4 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::STONE()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::STONE()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c pierre à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a pierre pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fPierre §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a5/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendCobblestoneForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::COBBLESTONE()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c pierre taillé.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 3){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c pierre taillé.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 3);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a pierre taillé pour un total de §e' . (int)$data[2] * 3 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::COBBLESTONE()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::COBBLESTONE()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c pierre taillé à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a pierre taillé pour §e$data[2].");
            }
        });
        $form->setTitle('§c- §fPierre taillé §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a3/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    public function sendDirtForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::DIRT()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c terre.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 20){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c terre.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 20);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a terre pour un total de §e' . (int)$data[2] * 20 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::DIRT()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::DIRT()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c terre à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a terre pour §e$data[2].");
            }
        });
        $form->setTitle('§c- §fTerre §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a20/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    private function sendAgricultureForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data){

            if (is_null($data))
                return;

            if ($data === 0)
                $this->sendCactusForm($player);
            elseif($data === 1)
                $this->sendPumpkinForm($player);
            elseif($data === 2)
                $this->sendWaterMelonForm($player);
            elseif($data === 3)
                $this->sendPotetoForm($player);
            elseif($data === 4)
                $this->sendCarrotForm($player);
            elseif($data === 5)
                $this->sendWheatForm($player);
            elseif($data === 6)
                $this->sendSeedForm($player);
            else
                $this->sendShopForm($player);

        });
        $form->setTitle('§c- §fAgriculture §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§f-------------------------------');
        $form->addButton('§7» §rCactus', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/cactus_side');
        $form->addButton('§7» §rCitrouille', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/pumpkin_side');
        $form->addButton('§7» §rPastèque', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/melon');
        $form->addButton('§7» §rPomme de terre', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/potato');
        $form->addButton('§7» §rCarrotes', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/carrot');
        $form->addButton('§7» §rBlé', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/wheat');
        $form->addButton('§7» §rGraine', SimpleForm::IMAGE_TYPE_PATH, 'textures/items/seeds_wheat');
        $form->addButton('§7» §cRetour', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/barrier');
        $player->sendForm($form);
    }

    public function sendCactusForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::CACTUS()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c cactus.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c cactus.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a cactus pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::CACTUS()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::CACTUS()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c cactus à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 5);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a cactus pour §e" . (int)$data[2] * 5 . ".");

            }
        });
        $form->setTitle('§c- §fCactus §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c5/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendPumpkinForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaBlocks::PUMPKIN()->asItem()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c citrouilles.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 225){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c citrouilles.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 225);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a citrouilles pour un total de §e' . (int)$data[2] * 225 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaBlocks::PUMPKIN()->asItem()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaBlocks::PUMPKIN()->asItem()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c citrouilles à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 3);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a citrouilles pour §e " . (int)$data[2] * 3 . ".");

            }
        });
        $form->setTitle('§c- §fCitrouille §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a225/u' . PHP_EOL . '§c3/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendWaterMelonForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::MELON()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c pastèque.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 225){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c pastèque.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 225);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a pastèque pour un total de §e' . (int)$data[2] * 225 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::MELON()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::MELON()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c pastèque à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 3);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a pastèque pour §e" . (int)$data[2] * 3 . ".");

            }
        });
        $form->setTitle('§c- §fPastèque §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a225/u' . PHP_EOL . '§c3/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendPotetoForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::POTATO()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c pomme de terre.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c pomme de terre.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a pomme de terre pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::POTATO()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::POTATO()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c pomme de terre à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a bois de sapin pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fPomme de terre §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendCarrotForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::CARROT()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c carrote.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c carrote.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a carrote pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::CARROT()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::CARROT()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c carrote à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a carrote pour §e$data[2].");

            }
        });
        $form->setTitle('§c- §fCarrote §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendWheatForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::WHEAT()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c blé.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 75){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c blé.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 75);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a blé pour un total de §e' . (int)$data[2] * 75 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::WHEAT()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::WHEAT()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c blé à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 2);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a blé pour §e" . (int)$data[2] * 2 . ".");
            }
        });
        $form->setTitle('§c- §fBlé §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a75/u' . PHP_EOL . '§c2/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    public function sendSeedForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::WHEAT_SEEDS()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c graine.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 10){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c graine.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 10);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a graine pour un total de §e' . (int)$data[2] * 10 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::WHEAT_SEEDS()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::WHEAT_SEEDS()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c graine à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a graine pour §e$data[2].");
            }
        });
        $form->setTitle('§c- §fGraine §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a10/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendOreForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            switch($data){
                case 0:
                    $this->sendFlintForm($player);
                    break;
                case 1:
                    $this->sendCoalForm($player);
                    break;
                case 2:
                    $this->sendIronForm($player);
                    break;
                case 3:
                    $this->sendGoldForm($player);
                    break;
                case 4:
                    $this->sendRedstoneForm($player);
                    break;
                case 5:
                    $this->sendDiamondForm($player);
                    break;
                case 6:
                    $this->sendLapisForm($player);
                    break;
                case 7:
                    $this->sendEmeraldForm($player);
                    break;
                default:
                    $this->sendShopForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fMinerais §c-");
        $form->addButton("§7» §rSilex", SimpleForm::IMAGE_TYPE_PATH, "textures/items/flint");
        $form->addButton("§7» §rCharbon", SimpleForm::IMAGE_TYPE_PATH, "textures/items/coal");
        $form->addButton("§7» §rFer", SimpleForm::IMAGE_TYPE_PATH, "textures/items/iron_ingot");
        $form->addButton("§7» §rOr", SimpleForm::IMAGE_TYPE_PATH, "textures/items/gold_ingot");
        $form->addButton("§7» §rRedstone", SimpleForm::IMAGE_TYPE_PATH, "textures/items/redstone_dust");
        $form->addButton("§7» §rDiamant", SimpleForm::IMAGE_TYPE_PATH, "textures/items/diamond");
        $form->addButton("§7» §rLapis Lazuli", SimpleForm::IMAGE_TYPE_PATH, "textures/blocks/lapis_block");
        $form->addButton("§7» §rÉmeraude", SimpleForm::IMAGE_TYPE_PATH, "textures/items/emerald");
        $form->addButton('§7» §cRetour', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/barrier');
        $player->sendForm($form);
    }

    private function sendFlintForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::FLINT()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c silex.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 15){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c silex.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 15);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a silex pour un total de §e' . (int)$data[2] * 15 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::FLINT()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::FLINT()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c silex à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a silex pour §e$data[2].");
            }
        });
        $form->setTitle('§c- §fSilex §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a15/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendCoalForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::COAL()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c charbon(s).");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 200){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c charbon(s).");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 200);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a charbon(s) pour un total de §e' . (int)$data[2] * 200 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::COAL()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::COAL()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c charbon(s) à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 5);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a charbon(s) pour §e" . $data[2] * 5 . " .");
            }
        });
        $form->setTitle('§c- §fCharbon §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a200/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendIronForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::IRON_INGOT()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c lingot(s) de fer.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 200){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c lingot(s) de fer.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 200);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a lingot(s) de fer pour un total de §e' . (int)$data[2] * 200 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::IRON_INGOT()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::IRON_INGOT()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c lingot(s) de fer à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 10);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a lingot(s) de fer pour §e" . $data[2] * 10 . " .");
            }
        });
        $form->setTitle('§c- §fFer §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a200/u' . PHP_EOL . '§c10/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendGoldForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::GOLD_INGOT()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c lingot(s) d'or.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 500){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c lingot(s) d'or.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 500);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a lingot(s) d\'or pour un total de §e' . (int)$data[2] * 500 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::GOLD_INGOT()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::GOLD_INGOT()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c lingot(s) d'or à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 30);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a lingot(s) d'or pour §e" . $data[2] * 30 . " .");
            }
        });
        $form->setTitle('§c- §fOr §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a500/u' . PHP_EOL . '§c30/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendRedstoneForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::REDSTONE_DUST()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c poudre(s) de redstone.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c poudre(s) de redstone.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a poudre(s) de redstone pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::REDSTONE_DUST()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::REDSTONE_DUST()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c poudre(s) de redstone à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 2);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a poudre(s) de redstone pour §e" . $data[2] * 2 . " .");
            }
        });
        $form->setTitle('§c- §fRedstone §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c2/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendDiamondForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::DIAMOND()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c diamant(s).");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 500){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c diamant(s).");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 500);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a lingot(s) d\'or pour un total de §e' . (int)$data[2] * 500 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::DIAMOND()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::DIAMOND()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c diamant(s) à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 30);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a diamant(s) pour §e" . $data[2] * 30 . " .");
            }
        });
        $form->setTitle('§c- §fDiamant §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a500/u' . PHP_EOL . '§c30/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }


    private function sendLapisForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::LAPIS_LAZULI()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c lapis lazuli.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 300){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c lapis lazuli.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 300);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a lapis lazuli pour un total de §e' . (int)$data[2] * 300 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::LAPIS_LAZULI()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::LAPIS_LAZULI()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c lapis lazuli à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 3);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a lapis lazuli pour §e" . $data[2] * 3 . " .");
            }
        });
        $form->setTitle('§c- §fLapis Lazuli §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a300/u' . PHP_EOL . '§c3/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);    }

    private function sendEmeraldForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::EMERALD()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c émeraude(s).");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 1000){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c émeraude(s).");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 1000);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a émeraude(s) pour un total de §e' . (int)$data[2] * 1000 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::EMERALD()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::EMERALD()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c émeraude(s) à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 40);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a émeraude(s) pour §e" . $data[2] * 40 . " .");
            }
        });
        $form->setTitle('§c- §fÉmeraude §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a1000/u' . PHP_EOL . '§c40/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendLootForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            switch($data){
                case 0:
                    $this->sendBoneForm($player);
                    break;
                case 1:
                    $this->sendRawBeefForm($player);
                    break;
                case 2:
                    $this->sendBeefForm($player);
                    break;
                case 3:
                    $this->sendRawSeepMeatForm($player);
                    break;
                case 4:
                    $this->sendSheepMeatForm($player);
                    break;
                default:
                    $this->sendShopForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fMinerais §c-");
        $form->addButton("§7» §rOs", SimpleForm::IMAGE_TYPE_PATH, "textures/items/bone");
        $form->addButton("§7» §rBoeuf cru", SimpleForm::IMAGE_TYPE_PATH, "textures/items/hoglin_meat_raw");
        $form->addButton("§7» §rBoeuf cuit", SimpleForm::IMAGE_TYPE_PATH, "textures/items/hoglin_meat_cooked");
        $form->addButton("§7» §rMouton cru", SimpleForm::IMAGE_TYPE_PATH, "textures/items/mutton_raw");
        $form->addButton("§7» §rmMouton cuit", SimpleForm::IMAGE_TYPE_PATH, "textures/items/mutton_cooked");
        $form->addButton('§7» §cRetour', SimpleForm::IMAGE_TYPE_PATH, 'textures/blocks/barrier');
        $player->sendForm($form);
    }

    private function sendBoneForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::BONE()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c d'os.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 300){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c d'os.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 300);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a d\'os pour un total de §e' . (int)$data[2] * 300 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::BONE()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::BONE()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c d'os à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2] * 30);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a d'os pour §e" . $data[2] * 30 . " .");
            }
        });
        $form->setTitle('§c- §fOs §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a300/u' . PHP_EOL . '§c30/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendRawBeefForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::RAW_BEEF()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c boeuf(s) cru.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 40){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c boeuf(s) cru.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 40);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a boeuf(s) cru pour un total de §e' . (int)$data[2] * 40 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::RAW_BEEF()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::RAW_BEEF()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c boeuf(s) cru à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a boeuf(s) cru pour §e" . $data[2] . " .");
            }
        });
        $form->setTitle('§c- §fBoeuf Cru §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a40/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendBeefForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::STEAK()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c boeuf(s) cuit(s).");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c boeuf(s) cuit(s).");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a boeuf(s) cuit(s) pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::STEAK()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::STEAK()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c boeuf(s) cuit(s) à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a boeuf(s) cuit(s) pour §e" . $data[2] . " .");
            }
        });
        $form->setTitle('§c- §fBoeuf Cuit §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendRawSeepMeatForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::RAW_MUTTON()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c mouton(s) cru.");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 40){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c mouton(s) cru.");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 40);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a mouton(s) cru pour un total de §e' . (int)$data[2] * 40 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::RAW_MUTTON()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::RAW_MUTTON()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c mouton(s) cru à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a mouton(s) cru pour §e" . $data[2] . " .");
            }
        });
        $form->setTitle('§c- §fMouton cru §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a40/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
        $form->addDropdown('§7» §rAction: ', ['§aAcheter', '§cVendre']);
        $form->addSlider('§7» §rNombre: ', 0, 64);
        $player->sendForm($form);
    }

    private function sendSheepMeatForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if (is_null($data))
                return;

            $action = ['§aAcheter', '$cVendre'];

            if ($action[$data[1]] === '§aAcheter'){
                $item = ItemFactory::getInstance()->get(VanillaItems::COOKED_MUTTON()->getId(), 0, (int)$data[2]);
                if (!$player->getInventory()->canAddItem($item)){
                    $player->sendMessage(Utils::getPrefix() . "§cVotre inventaire est complet vous ne pouvez donc pas acheter §e" . (int)$data[2] . "§c mouton(s) cuit(s).");
                    return;
                }

                if ($this->core->getEconomyManager()->getMoney($player->getName()) < (int)$data[2] * 50){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'argent pour acheter §e" . (int)$data[2] . "§c mouton(s) cuit(s).");
                    return;
                }

                $this->core->getEconomyManager()->delMoney($player->getName(), (int)$data[2] * 50);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez acheté §e" . (int)$data[2] . '§a mouton(s) cuit(s) pour un total de §e' . (int)$data[2] * 50 . '§a.');
            }else{
                $item = $this->countItem($player, VanillaItems::COOKED_MUTTON()->getId());
                $itemSell = ItemFactory::getInstance()->get(VanillaItems::COOKED_MUTTON()->getId(), 0, (int)$data[2]);
                if ((int)$data[2] > $item){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas §e" . (int)$data[2] . "§c mouton(s) cuit(s) à vendre.");
                    return;
                }
                $this->core->getEconomyManager()->addMoney($player->getName(), (int)$data[2]);
                $player->getInventory()->removeItem($itemSell);
                $player->sendMessage(Utils::getPrefix() . "§aVous avez vendu §e" . (int)$data[2] . "§a mouton(s) cuit(s) pour §e" . $data[2] . " .");
            }
        });
        $form->setTitle('§c- §fMouton cuit §c-');
        $form->setContent('-------------------------------' . PHP_EOL . '§7» §rVous avez actuellement §e' . $this->core->getEconomyManager()->getMoney($player->getName()) . '' . PHP_EOL . '§a50/u' . PHP_EOL . '§c1/u' . PHP_EOL . '§f-------------------------------');
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