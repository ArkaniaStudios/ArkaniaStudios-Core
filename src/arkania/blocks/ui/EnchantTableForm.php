<?php

namespace arkania\blocks\ui;

use arkania\libs\form\SimpleForm;
use arkania\utils\Utils;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\player\Player;

class EnchantTableForm
{
    private const NoXpMessage = "(§c!§f) §r§cVous n'avez pas assez de niveaux pour enchanter cet item !";

    public function sendEnchantTable(Player $player): void
    {
        $item = $player->getInventory()->getItemInHand();
        switch($item){
            case $item instanceof Sword:
                $this->sendSwordForm($player);
                break;
            case $item instanceof Tool && !$item instanceof Sword:
                $this->sendToolsForm($player);
                break;
            case $item instanceof Armor:
                $this->sendArmorForm($player);
                break;
            case $item->getId() === 0 && $item->getMeta() === 0:
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas enchanter votre main !");
                break;
        }
    }

    public function sendArmorForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    if(!$player->getInventory()->getItemInHand() instanceof Armor) {
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    $this->sendProtectionForm($player);
                    break;
                case 1:
                    if(!$player->getInventory()->getItemInHand() instanceof Armor) {
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    $this->sendUnbreakingForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fArmure §c-");
        $form->addButton("Protection");
        $form->addButton("Solidité");
        $player->sendForm($form);
    }

    public function sendProtectionForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec protection 1");
                    break;
                case 1:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec protection 2");
                    break;
                case 2:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec protection 3");
                    break;
                case 3:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 40){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 40);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec protection 4");
                    break;
                case 4:
                    $this->sendArmorForm($player);
            }
        });
        $form->setTitle("§c- §fProtection §c-");
        $form->addButton("Protection 1");
        $form->addButton("Protection 2");
        $form->addButton("Protection 3");
        $form->addButton("Protection 4");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function sendUnbreakingForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 1");
                    break;
                case 1:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 2");
                    break;
                case 2:
                    if(!$item instanceof Armor){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une armure !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 3");
                    break;
                case 3:
                    $this->sendArmorForm($player);
            }
        });
        $form->setTitle("§c- §fSolidité §c-");
        $form->addButton("Solidité 1");
        $form->addButton("Solidité 2");
        $form->addButton("Solidité 3");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function sendSwordForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    $this->sharpnessForm($player);
                    break;
                case 1:
                    if(!$item instanceof Sword) {
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    $this->unbreakingForm($player);
                    break;
                case 2:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    $this->fireAspectForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fépée §c-");
        $form->addButton("Tranchant");
        $form->addButton("Solidité");
        $form->addButton("Aura de feu");
        $player->sendForm($form);
    }

    public function sharpnessForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec tranchant 1");
                    break;
                case 1:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec tranchant 2");
                    break;
                case 2:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec tranchant 3");
                    break;
                case 3:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 40){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 4));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 40);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec tranchant 4");
                    break;
                case 4:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 50){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 50);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec tranchant 5");
                    break;
                case 5:
                    $this->sendSwordForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fTranchant §c-");
        $form->addButton("Tranchant 1");
        $form->addButton("Tranchant 2");
        $form->addButton("Tranchant 3");
        $form->addButton("Tranchant 4");
        $form->addButton("Tranchant 5");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function unbreakingForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 1");
                    break;
                case 1:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 2");
                    break;
                case 2:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 3");
                    break;
                case 3:
                    $this->sendSwordForm($player);
            }
        });
        $form->setTitle("§c- §fSolidité §c-");
        $form->addButton("Solidité 1");
        $form->addButton("Solidité 2");
        $form->addButton("Solidité 3");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function fireAspectForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 1");
                    break;
                case 1:
                    if(!$item instanceof Sword){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas une épée !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 2");
                    break;
                case 2:
                    $this->sendSwordForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fAura de feu §c-");
        $form->addButton("Aura de feu 1");
        $form->addButton("Aura de feu 2");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function sendToolsForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $this->sendEfficiencyForm($player);
                    break;
                case 1:
                    $this->sendUnbreaking($player);
                    break;
            }
        });
        $form->setTitle("§c- §fOutils §c-");
        $form->addButton("Efficacité");
        $form->addButton("Solidité");
        $player->sendForm($form);
    }

    public function sendEfficiencyForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec efficacité 1");
                    break;
                case 1:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec efficacité 2");
                    break;
                case 2:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec efficacité 3");
                    break;
                case 3:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 40){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 40);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec efficacité 4");
                    break;
                case 4:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 50){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 50);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec efficacité 5");
                    break;
                case 5:
                    $this->sendToolsForm($player);
                    break;
            }
        });
        $form->setTitle("§c- Efficacité §c-");
        $form->addButton("Efficacité 1");
        $form->addButton("Efficacité 2");
        $form->addButton("Efficacité 3");
        $form->addButton("Efficacité 4");
        $form->addButton("Efficacité 5");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }

    public function sendUnbreaking(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            switch($data){
                case 0:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 10){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 10);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 1");
                    break;
                case 1:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 2");
                    break;
                case 2:
                    if(!$item instanceof Tool){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas un outil !");
                        break;
                    }
                    if($player->getXpManager()->getXpLevel() < 30){
                        $player->sendMessage(self::NoXpMessage);
                        break;
                    }
                    $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
                    $player->getInventory()->setItemInHand($item);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 30);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a bien été enchanté avec solidité 3");
                    break;
                case 3:
                    $this->sendSwordForm($player);
            }
        });
        $form->setTitle("§c- §fSolidité §c-");
        $form->addButton("Solidité 1");
        $form->addButton("Solidité 2");
        $form->addButton("Solidité 3");
        $form->addButton("§cretour");
        $player->sendForm($form);
    }
}