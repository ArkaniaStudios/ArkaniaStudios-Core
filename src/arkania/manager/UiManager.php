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

namespace arkania\manager;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\data\SettingsNameIds;
use arkania\entity\base\BaseEntity;
use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;
use function PHPUnit\TestFixture\func;

final class UiManager {

    /** @var FactionManager */
    private FactionManager $factionManager;

    public function __construct() {
        $this->factionManager = new FactionManager();
    }

    /**
     * @param Player $player
     * @param BaseEntity $entity
     * @return void
     */
    public function sendMenuForm(Player $player, BaseEntity $entity): void {
        $form = new SimpleForm(function (Player $player, $data) use ($entity){
            if (is_null($data))
                return;

            switch ($data){
                case 0:
                    $this->sendAddCommandForm($player, $entity);
                    break;
                case 1:
                    //$this->sendDelCommandForm($player, $entity);
                    $player->sendMessage(Utils::getPrefix() . "§cindisponible");
                    break;
                case 2:
                    $this->sendChangeNameForm($player, $entity);
                    break;
                case 3:
                    $this->sendChangeSizeForm($player, $entity);
                    break;
                case 4:
                    $entity->flagForDespawn();
                    $player->sendMessage(Utils::getPrefix() . "Vous avez bien supprimé l'entité.");
                    break;
            }
        });
        $form->setTitle('§c- §fNpcManager §c-');
        $form->addButton('§7» §rAjouter une commande');
        $form->addButton('§7» §rRetirer une commande');
        $form->addButton('§7» §rChanger le nom');
        $form->addButton('§7» §rChanger la taille');
        $form->addButton('§7» §rRetirer le NPC');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param BaseEntity $entity
     * @return void
     */
    private function sendAddCommandForm(Player $player, BaseEntity $entity): void {
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if (is_null($data))
                return;

            $entity->addCommand($data[1]);
            $player->sendMessage(Utils::getPrefix() . "Vous avez ajouter la commande §c" . $data[1] . "§f.");
        });
        $form->setTitle('§c- §fAddCommand §c-');
        $form->setContent("§7» §rVoici l'interface d'ajout d'une commande. Mettez le nom de la commande + les arguments. Les commandes seront exécutés par le joueur.");
        $form->addInput('§7» §rNom de la commande :', 'ex: /msg Julien8436 Salut');
        $player->sendForm($form);
    }

    /*private function sendDelCommandForm(Player $player, BaseEntity $entity): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($entity) {
            if (is_null($data))
                return;

            $entity->removeCommand($data[1]);
            $player->sendMessage(Utils::getPrefix() . "Vous avez supprimé la commande §c" . $entity->getCommand()[$data[1]] . "§f.");
        });

        $form->setTitle('§c- §fDelCommand §c-');
        $form->setContent('§7» §rSéléctionnez la commande que vous souhaitez supprimer.');
        $form->addDropdown('§7» §rListe des commandes :', $entity->commands);
        $player->sendForm($form);
    }*/

    /**
     * @param Player $player
     * @param BaseEntity $entity
     * @return void
     */
    private function sendChangeNameForm(Player $player, BaseEntity $entity): void{
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if (is_null($data))
                return;
            $entity->setCustomName($data[0]);
            $player->sendMessage(Utils::getPrefix() . "Vous avez changé le nom de l'entité en §c$data[0]§f.");
        });
        $form->setTitle('§c- §fChangeName §c-');
        $form->addInput('§7» §rNouveau nom :');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param BaseEntity $entity
     * @return void
     */
    private function sendChangeSizeForm(Player $player, BaseEntity $entity): void {
        $form = new CustomForm(function (Player $player, $data) use ($entity){
            if (is_null($data))
                return;

            $entity->setTaille($data[0]);
            $player->sendMessage(Utils::getPrefix() . "Vous avez définit la taille du npc à §c" . $data[0] . "§f.");

        });
        $form->setTitle('§c- §fChangeSize §c-');
        $form->addSlider('§7» §rTaille:', 1, 3, -1,1);
        $player->sendForm($form);
    }

    public function sendSettingsForm(Player $player): void {
        $menu = InvMenu::create();
    }

    public function sendCreateFactionForm(Player $player): void {
        $form = new CustomForm(function (Player $player, $data){

            $factionManager = $this->factionManager;

            if (is_null($data))
                return;

            if (is_null($data[1])) {
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nom pour votre faction");
                return;
            }

            if (!Utils::isValidArgument($data[1])){
                $player->sendMessage(Utils::getPrefix() . "§cUn argument de votre faction n'est pas valide. Merci de le changer.");
                return;
            }

            if (strlen($data[1]) > 10){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre plus de 10 caractères.");
                return;
            }

            if (!$factionManager->getFactionClass($data[1], $player->getName())->existFaction()) {
                $player->sendMessage(Utils::getPrefix() . "§cCette faction existe déjà.");
                return;
            }

            $jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            $mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
            $num_jour = date('w');
            $jour = $jours[$num_jour];
            $num_mois = date('n') - 1;
            $mois = $mois[$num_mois];
            $annee = date('Y');

            $inscription = $jour . ' ' . date('d') . ' ' . $mois . ' ' . $annee;

            $description = $data[2] ?? '';

            $factionManager->getFactionClass($data[1], $player->getName(), $inscription, $description)->createFaction();
            BaseCommand::sendToastPacket($player, '§7-> §fFACTION', '§aVOUS VENEZ DE CREER LA FACTION §2' . $data[1] . "§a.");
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§c" . $player->getName() . "§f vient de créer la faction §c" . $data[1] . "§f.");
        });
        $form->setTitle('§c- §fFaction §c-');
        $form->setContent("§7» §rBienvenue dans l'interface de création de votre faction. Précisez le nom de votre faction afin de la créer. Vous pouvez aussi préciser une description mais celle-ci est facultative.");
        $form->addInput('§7» §rNom');
        $form->addInput('§7» §rDescription');
        $player->sendForm($form);
    }

    public function sendFactionInfoForm(Player $player, string $faction): void {
        $form = new SimpleForm(function(Player $player, $data) use ($faction){

        });
        $form->setTitle('§c- §fFaction §c-');
        $form->setContent("§7» §rVoici les informations de la faction : §c" . $faction . "§f.");
    }
}