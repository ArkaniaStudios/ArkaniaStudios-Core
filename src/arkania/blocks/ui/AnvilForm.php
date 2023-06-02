<?php

namespace arkania\blocks\ui;

use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\utils\Utils;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;

class AnvilForm
{
    public function sendAnvilForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $this->sendRepairForm($player);
                    break;
                case 1:
                    $this->sendRenameForm($player);
                    break;
            }
        });
        $form->setTitle("§c- §fEnclume §c-");
        $form->addButton("Réparer");
        $form->addButton("Renommer");
        $player->sendForm($form);
    }

    public function sendRepairForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $item = $player->getInventory()->getItemInHand();
                    if(!($item instanceof Armor or $item instanceof Tool)){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'est pas réparable !");
                        return;
                    }
                    if($player->getXpManager()->getXpLevel() < 20){
                        $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'xp pour réparer votre item !");
                        return;
                    }
                    if($item->getMeta() === $item->getMaxDurability()){
                        $player->sendMessage(Utils::getPrefix() . "§cVotre item n'a pas besoin d'être réparé !");
                        return;
                    }
                    $item->setDamage(0);
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 20);
                    $player->getInventory()->setItemInHand($item);
                    $player->sendMessage(Utils::getPrefix() . "Votre item a été réparé avec §asuccès !");
            }
        });
        $form->setTitle("§c- §fRéparer §c-");
        $form->setContent("Souhaitez vous réparer votre item pour 20xp ?");
        $form->addButton("Réparer");
        $player->sendForm($form);
    }

    public function sendRenameForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data){
            if(is_null($data)) return;

            $item = $player->getInventory()->getItemInHand();

            if($player->getXpManager()->getXpLevel() < 15){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas assez d'xp pour renommer votre item !");
                return;
            }

            $item->setCustomName($data[1]);
            $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 15);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(Utils::getPrefix() . "Vous venez de renommer votre item en " . $data[1]);
        });
        $form->setTitle("§c- §fRenommer §c-");
        $form->setContent("Voulez vous renommer votre item pour 15xp ?");
        $form->addInput("", "name:string");
        $player->sendForm($form);
    }
}