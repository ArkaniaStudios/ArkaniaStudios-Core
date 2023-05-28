<?php

namespace arkania\manager;

use arkania\Core;
use arkania\entities\base\CustomEntity;
use arkania\entities\base\SimpleEntity;
use arkania\entities\type\DeathLeaderBoardEntity;
use arkania\entities\type\FloatingText;
use arkania\entities\type\HumanEntity;
use arkania\entities\type\KillLeaderBoardEntity;
use arkania\entities\type\MoneyLeaderBoardEntity;
use arkania\entities\type\VillagerEntity;
use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\utils\Utils;
use pocketmine\player\Player;

class EntityFormManager
{
    private Core $core;

    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    public function sendEntityForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data): void {
            if(is_null($data))
                return;
            switch ($data){
                case 0:
                    $humain = new HumanEntity($player->getLocation(), $player->getSkin());
                    $this->sendParametreForm($player, $humain);
                    break;
                case 1:
                    $villager = new VillagerEntity($player->getLocation());
                    $this->sendParametreForm($player, $villager);
                    break;
                case 2:
                    $floatingtext = new FloatingText($player->getLocation());
                    $floatingtext->setImmobile();
                    $this->sendParametreForm($player, $floatingtext);
                    break;
                case 3:
                    $leaderboardmoney = new MoneyLeaderBoardEntity($player->getLocation());
                    $leaderboardmoney->setImmobile();
                    $this->sendParametreForm($player, $leaderboardmoney);
                    break;
                case 4:
                    $leaderboarddeath = new DeathLeaderBoardEntity($player->getLocation());
                    $leaderboarddeath->setImmobile();
                    $this->sendParametreForm($player, $leaderboarddeath);
                    break;
                case 5:
                    $leaderboardkill = new KillLeaderBoardEntity($player->getLocation());
                    $leaderboardkill->setImmobile();
                    $this->sendParametreForm($player, $leaderboardkill);
                    break;
            }
        });
        $form->setTitle('§f- §cNPC§f -');
        $form->addButton('Humain');
        $form->addButton('Villageois');
        $form->addButton('Floating Text');
        $form->addButton('LeaderBoard §aMoney');
        $form->addButton('LeaderBoard §cMorts');
        $form->addButton('LeaderBoard §7Kills');
        $player->sendForm($form);
    }

    public function sendEntityItemForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new SimpleForm(function (Player $player, $data) use ($entity): void {
            if(is_null($data))
                return;
            switch($data){
                case 0:
                    $this->sendModifyCustomNameForm($player, $entity);
                    break;
                case 1:
                    $this->sendTailleForm($player,  $entity);
                    break;
                case 2:
                    $this->sendRotationForm($player, $entity);
                    break;
                case 3:
                    $this->changeEntitySkinForm($player, $entity);
                    break;
                case 4:
                    $this->addEntityCommandForm($player, $entity);
                    break;
                case 5:
                    $this->delEntityCommandForm($player, $entity);
                    break;
                case 6:
                    $this->sendEntityInventoryForm($player, $entity);
                    break;
                case 7:
                    $entity->kill();
                    break;
            }
        });
        $form->setTitle('§f- §cNPC§f -');
        $form->addButton('» Name');
        $form->addButton('» Taille');
        $form->addButton('» Rotation');
        $form->addButton('» Skin');
        $form->addButton('» §aAjouter §fcommande');
        $form->addButton('» §cSupprimer §fcommande');
        $form->addButton('» Inventaire');
        $form->addButton('» §cSupprimer');
        $player->sendForm($form);
    }

    private function sendParametreForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new SimpleForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $this->sendCustomNameForm($player, $entity);
                    break;
                case 1:
                    $this->sendTailleForm($player,  $entity);
                    break;
                case 2:
                    $this->sendRotationForm($player, $entity);
                    break;
                case 3:
                    $entity->setNpc();
                    $entity->spawnToAll();
            }
        });
        $form->setTitle('§c- §fParamètres§f §c-');
        $form->addButton('» Nom');
        $form->addButton('» Taille');
        $form->addButton('» Rotation');
        $form->addButton('» Spawn');
        $player->sendForm($form);
    }

    private function sendCustomNameForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity): void{
            if(is_null($data)) return;
            $entity->setCustomName($data[0]);
            $entity->setNameTag(str_replace('{LINE}', "\n", $data[0]));
            $entity->setNpc();
            $entity->spawnToAll();
            $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le nom de l\'entitée ');
        });
        $form->setTitle('§c- §fName §c-');
        $form->addInput('Name', 'nom: string');
        $player->sendForm($form);
    }

    private function sendModifyCustomNameForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity): void{
            if(is_null($data)) return;
            $entity->setCustomName($data[0]);
            $entity->setNameTag(str_replace('{LINE}', "\n", $data[0]));
            $entity->setNpc();
            $entity->spawnToAll();
        });
        $form->setTitle('§c- §fName §c-');
        $form->addInput('Name', 'nom: string');
        $player->sendForm($form);
    }

    private function sendRotationForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity): void{
            if(is_null($data)) return;
            $entity->setRotation((float)$data[1], (float)$data[0]);
            $entity->setPitch((float)$data[0]);
            $entity->setYaw((float)$data[1]);
            $entity->setRotation($data[1], $data[0]);
            $entity->setNpc();
            $entity->spawnToAll();
            $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer la rotation de l\'entitée');
        });
        $form->setTitle('§c- §Rotation §c-');
        $form->addInput('» Pitch');
        $form->addInput('» Yaw');
        $player->sendForm($form);
    }

    private function sendTailleForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            if((float)$data[0] < 0.1 or (float)$data[0] > 10){
                $player->sendMessage('§cLa taille est invalide !');
                return;
            }
            $entity->setScale((float)$data[0]);
            $entity->setNpc();
            $entity->spawnToAll();
            $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le nom de l\'entitée');
        });
        $form->setTitle('§c- §fTaille§f §c-');
        $form->addInput('Taille', 'minimum: 0.1 maximum: 10');
        $player->sendForm($form);
    }

    public function changeEntitySkinForm(Player $player, CustomEntity|SimpleEntity $entity): void  {
        $playerList = [];
        foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer){
            $playerList[] = $onlinePlayer->getName();
        }
        $form = new CustomForm(function (Player $player, $data) use ($entity, $playerList){
            if(is_null($data)) return;
            $target = $playerList[$data[0]];
            $playerName = $this->core->getServer()->getPlayerExact($target);
            if(!$playerName instanceof Player) return;
            $entity->setSkin($playerName->getSkin());
            $entity->sendSkin();
        });
        $form->setTitle('§c- §fSkin §c-');
        $form->addDropdown('Liste des joueurs :', $playerList);
        $player->sendForm($form);
    }

    public function addEntityCommandForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            $entity->addCommand($data[0], (string)$data[1]);
            $player->sendMessage(Utils::getPrefix() . 'Vous venez d\'ajouter la commande : ' . $data[1] . ' au npc');
        });
        $form->setTitle('§c- §fCommand §c-');
        $form->addDropdown('§7» §rType d\'exécution: ', ['Joueur', 'Console']);
        $form->addInput('commande à ajouter', 'commande:string (exemple : discord)');
        $player->sendForm($form);
    }

    public function delEntityCommandForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            if(!$entity->hasCommands($data[1])){
                $player->sendMessage(Utils::getPrefix() . '§cCette commande n\'existe pas !');
                return;
            }
            $entity->removeCommand($data[1]);
            $player->sendMessage(Utils::getPrefix() . 'Vous venez de §csupprimer§f la commande du NPC');
        });
        $form->setTitle('§c- §fCommand §c-');
        $form->setContent('Liste des commandes du NPC :' . $entity->listCommands());
        $form->addInput('commande', 'commande:string');
        $player->sendForm($form);
    }

    public function sendEntityInventoryForm(Player $player, CustomEntity|SimpleEntity $entity): void
    {
        $form = new SimpleForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $entity->getInventory()->setItemInHand($player->getInventory()->getItemInHand());
                    $player->sendMessage(Utils::getPrefix() . 'Vous venez de §adéfinir§f l\'item en main du NPC');
                    break;
                case 1:
                    $this->sendEntityArmorForm($player, $entity);
                    break;
            }
        });
        $form->setTitle('§c- §fInventaire §c-');
        $form->addButton('» Main');
        $form->addButton('» Armure');
        $player->sendForm($form);
    }

    public function sendEntityArmorForm(Player $player, SimpleEntity|CustomEntity $entity): void
    {
        $form = new SimpleForm(function (Player $player, $data) use ($entity){
            if(is_null($data)) return;
            switch($data){
                case 0:
                    $entity->getArmorInventory()->setHelmet($player->getArmorInventory()->getHelmet());
                    $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le casque de l\'entitée');
                    break;
                case 1:
                    $entity->getArmorInventory()->setChestplate($player->getArmorInventory()->getChestplate());
                    $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le plastron de l\'entitée');
                    break;
                case 2:
                    $entity->getArmorInventory()->setLeggings($player->getArmorInventory()->getLeggings());
                    $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le pantalon de l\'entitée');
                    break;
                case 3:
                    $entity->getArmorInventory()->setBoots($player->getArmorInventory()->getBoots());
                    $player->sendMessage(Utils::getPrefix() . 'Vous venez de changer le casque de l\'entitée');
                    break;
            }
        });
        $form->setTitle('§c- §fArmure §c-');
        $form->addButton('» Casque');
        $form->addButton('» Plastron');
        $form->addButton('» Pantalon');
        $form->addButton('» Bottes');
        $player->sendForm($form);
    }
}