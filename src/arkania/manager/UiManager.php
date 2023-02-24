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

use arkania\commands\player\ServerSelectorCommand;
use arkania\Core;
use arkania\data\SettingsNameIds;
use arkania\data\WebhookData;
use arkania\entity\base\BaseEntity;
use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use arkania\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use arkania\libs\muqsit\invmenu\type\InvMenuTypeIds;
use arkania\tasks\TransfertTask;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use arkania\commands\BaseCommand;

final class UiManager {

    use Date;

    /** @var FactionManager */
    private FactionManager $factionManager;

    /** @var array */
    public static array $faction_webhook = [];

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

    /**
     * @param Player $player
     * @return void
     */
    public function sendSettingsForm(Player $player): void {

        $settings = new SettingsManager($player);
        if ($settings->getSettings(SettingsNameIds::CLEARLAG) === false)
            $statusC = '§aActivé';
        else
            $statusC = '§cDésactivé';

        if ($settings->getSettings(SettingsNameIds::MESSAGE) === false)
            $statusM = '§aActivé';
        else
            $statusM = '§cDésactivé';

        if ($settings->getSettings(SettingsNameIds::TELEPORT) === false)
            $statusT = '§aActivé';
        else
            $statusT = '§cDésactivé';

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName('             §c- §fSettings §c-');
        $glass = $this->setBaseInventoryConfig($menu);
        $menu->getInventory()->setItem(20, VanillaItems::CLOCK()->setCustomName('§7-> §fClearLag Message')->setLore(["\n", '§7-> §f' . $statusC]));
        $menu->getInventory()->setItem(24, VanillaItems::PAPER()->setCustomName('§7-> §fMessage')->setLore(["\n", '§7-> §f' . $statusM]));
        $menu->getInventory()->setItem(31, VanillaItems::SNOWBALL()->setCustomName('§7-> §fTeleport')->setLore(["\n", '§7-> §f' . $statusT]));
        $menu->getInventory()->setItem(36, $glass);
        $menu->getInventory()->setItem(44, $glass);
        $menu->getInventory()->setItem(45, $glass);
        $menu->getInventory()->setItem(46, $glass);
        $menu->getInventory()->setItem(52, $glass);
        $menu->getInventory()->setItem(53, $glass);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult{
            $player = $transaction->getPlayer();
            $settings = new SettingsManager($player);

            if ($transaction->getItemClicked()->getId() === VanillaItems::CLOCK()->getId()){
                if ($settings->getSettings(SettingsNameIds::CLEARLAG) === false)
                    $settings->setSettings(SettingsNameIds::CLEARLAG, true);
                else
                    $settings->setSettings(SettingsNameIds::CLEARLAG, false);
                $player->removeCurrentWindow();
                $this->sendSettingsForm($player);
            }elseif ($transaction->getItemClicked()->getId() === VanillaItems::PAPER()->getId()){
                if ($settings->getSettings(SettingsNameIds::MESSAGE) === false)
                    $settings->setSettings(SettingsNameIds::MESSAGE, true);
                else
                    $settings->setSettings(SettingsNameIds::MESSAGE, false);
                $player->removeCurrentWindow();
                $this->sendSettingsForm($player);
            }elseif ($transaction->getItemClicked()->getId() === VanillaItems::SNOWBALL()->getId()){
                if ($settings->getSettings(SettingsNameIds::TELEPORT) === false)
                    $settings->setSettings(SettingsNameIds::TELEPORT, true);
                else
                    $settings->setSettings(SettingsNameIds::TELEPORT, false);
                $player->removeCurrentWindow();
                $this->sendSettingsForm($player);
            }
            return $transaction->discard();
        });
        $menu->send($player);
    }

    /**
     * @param Player $player
     * @return void
     */
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

            if (str_contains($data[1], '§')){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre des caractères qui à pour but de colorer le nom de votre faction.");
                return;
            }

            if ($factionManager->getFactionClass($data[1], $player->getName())->existFaction()) {
                $player->sendMessage(Utils::getPrefix() . "§cCette faction existe déjà.");
                return;
            }

            $inscription = $this->dateFormat();

            $description = $data[2] ?? 'Aucune';

            if (strlen($description) > 50){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre plus de §e50 caractères §cdans la description de votre faction.");
                return;
            }

            $url = 'unknow';

            if ((bool)$data[3] === false){
                self::$faction_webhook[$player->getName()] = $player->getName();
                $player->sendMessage(Utils::getPrefix() . "§6Merci de mettre l'url du webhook dans le chat afin d'activer les logs pour votre faction.");
            }

            $factionManager->getFactionClass($data[1], $player->getName(), (bool)$data[3], $inscription, $description, $url)->createFaction();
            BaseCommand::sendToastPacket($player, '§7-> §fFACTION', '§aVOUS VENEZ DE CREER LA FACTION §e' . $data[1] . "§a.");
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§e" . $player->getName() . "§f vient de créer la faction §e" . $data[1] . "§f.");
            Core::getInstance()->ranksManager->updateNameTag($player);
        });
        $form->setTitle('§c- §fFaction §c-');
        $form->setContent("§7» §rBienvenue dans l'interface de création de votre faction. Précisez le nom de votre faction afin de la créer. Vous pouvez aussi préciser une description mais celle-ci est facultative.");
        $form->addInput('§7» §rNom');
        $form->addInput('§7» §rDescription');
        $form->addDropdown("§7» §rLogs discord :", ['§aActivé', '§cDésactivé']);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param string $faction
     * @return void
     */
    public function sendFactionInfoForm(Player $player, string $faction): void {
        $form = new SimpleForm(function(Player $player, $data) {

        });
        $factionManager = new FactionManager();
        $factionInfo = $factionManager->getFactionClass($faction, $player->getName());
        $description = $factionInfo->getDescription() === ' ' ? 'Aucune' : $factionInfo->getDescription();
        $form->setTitle('§c- §fFaction §c-');
        $form->setContent("§7» §rVoici les informations de la faction : §e" . $faction . "§f.\n\nChef de faction: §e" . $factionInfo->getOwner() . "\n§fDate de création: §e" . $factionInfo->getCreationDate() . "\n§fDescription: §e" . $description . "\n§fPower: §e" . $factionInfo->getPower() . "\n§fMoney: §e" . $factionInfo->getMoney() . "\n\n");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendServerSelectorForm(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName('             §c- §fServers §c-');
        if ($player->hasPermission('arkania:permission.selector.staff'))
            $menu->getInventory()->setItem(8, VanillaItems::STICK()->setCustomName('§9Server Développement')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(20, VanillaItems::DIAMOND_SWORD()->setCustomName('§6Thêta')->setLore(['Faction #1'])->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(22, VanillaItems::DIAMOND_SWORD()->setCustomName('§aZeta')->setLore(['Faction #2'])->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(24, VanillaItems::DIAMOND_SWORD()->setCustomName('§7Epsilon')->setLore(['Faction #3'])->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(28, VanillaItems::IRON_PICKAXE()->setCustomName('§8Minage #1')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(30, VanillaItems::IRON_PICKAXE()->setCustomName('§8Minage #2')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(32, VanillaItems::IRON_PICKAXE()->setCustomName('§8Minage #3')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(34, VanillaItems::GOLDEN_PICKAXE()->setCustomName('§8Minage #4')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->getInventory()->setItem(40, VanillaItems::COMPASS()->setCustomName('§eLobby')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 10)));
        $menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
            $player = $transaction->getPlayer();
            $scheduler = Core::getInstance()->getScheduler();
            $serverStatus = Core::getInstance()->serverStatus;

            if ($transaction->getItemClicked()->getCustomName() === '§6Thêta'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Theta') === '§cFermé' || $serverStatus->getServerStatus('Theta') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Theta') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('faction', 1, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§aZeta'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Zeta') === '§cFermé' || $serverStatus->getServerStatus('Zeta') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Zeta') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('faction', 2, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }

                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§7Epsilon'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Epsilon') === '§cFermé' || $serverStatus->getServerStatus('Epsilon') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Epsilon') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('faction', 3, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§8Minage #1'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Minage1') === '§cFermé' || $serverStatus->getServerStatus('Minage1') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Minage1') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 1, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§8Minage #2'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Minage2') === '§cFermé' || $serverStatus->getServerStatus('Minage2') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Minage2') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 2, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§8Minage #3'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Minage3') === '§cFermé' || $serverStatus->getServerStatus('Minage3') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Minage3') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 3, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§8Minage #4'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    if ($serverStatus->getServerStatus('Minage4') === '§cFermé' || $serverStatus->getServerStatus('Minage4') === false){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Theta') === '§6Maintenance'){
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                         ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                         $player->removeCurrentWindow();
                         $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 4, $player), 20);
                         $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '§eLobby'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    $player->removeCurrentWindow();
                    if ($serverStatus->getServerStatus('Lobby1') === '§cFermé' || $serverStatus->getServerStatus('Lobby1') === false){
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('Lobby1') === '§6Maintenance'){
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('lobby', 1, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }elseif($transaction->getItemClicked()->getCustomName() === '9Server Développement'){
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])){
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                }else{
                    $player->removeCurrentWindow();
                    if ($serverStatus->getServerStatus('ServerTest') === '§cFermé' || $serverStatus->getServerStatus('ServerTest') === false){
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    }elseif($serverStatus->getServerStatus('ServerTest') === '§6Maintenance'){
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    }else{
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('dev', 1, $player), 20);
                        $player->sendMessage(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            }

            return $transaction->discard();
        });
        $menu->send($player);
    }

    /**
     * @param InvMenu $menu
     * @return Item
     */
    private function setBaseInventoryConfig(InvMenu $menu): Item
    {
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName(' ');
        $menu->getInventory()->setItem(0, $glass);
        $menu->getInventory()->setItem(1, $glass);
        $menu->getInventory()->setItem(7, $glass);
        $menu->getInventory()->setItem(8, $glass);
        $menu->getInventory()->setItem(9, $glass);
        $menu->getInventory()->setItem(17, $glass);
        return $glass;
    }

    /**
     * @param Player $player
     * @param bool $isAdmin
     * @return void
     */
    public function sendKitForm(Player $player, bool $isAdmin = false): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName('            §cKits');
        $menu->getInventory()->setItem(10, VanillaItems::RECORD_WAIT()->setCustomName('Kit §7Joueur'));
        $menu->getInventory()->setItem(11, VanillaItems::RECORD_13()->setCustomName('Kit §dBooster'));
        $menu->getInventory()->setItem(13, VanillaItems::RECORD_STAL()->setCustomName('Kit §eNoble'));
        $menu->getInventory()->setItem(14, VanillaItems::RECORD_WARD()->setCustomName('Kit §6Héro'));
        $menu->getInventory()->setItem(15, VanillaItems::RECORD_MELLOHI()->setCustomName('Kit §4Seigneur'));
        $menu->getInventory()->setItem(16, VanillaItems::RECORD_CAT()->setCustomName('Kit §cVidéaste'));
        $menu->setListener(function (InvMenuTransaction $transaction) use ($isAdmin): InvMenuTransactionResult {

            $player = $transaction->getPlayer();
            $kits = Core::getInstance()->kits;

            if ($transaction->getItemClicked()->getCustomName() === 'Kit §7Joueur') {
                $player->removeCurrentWindow();
                $kits->sendKitPlayer($player, $isAdmin);
            }elseif($transaction->getItemClicked()->getCustomName() === 'Kit §dBooster') {
                if ($player->hasPermission('arkania:permission.kit.booster')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitBooster($player, $isAdmin);
                }else{
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit;");
                }
            }elseif($transaction->getItemClicked()->getCustomName() === 'Kit §eNoble') {
                if ($player->hasPermission('arkania:permission.kit.noble')){
                    $player->removeCurrentWindow();
                    $kits->sendKitNoble($player, $isAdmin);
                }else{
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit;");
                }
            }elseif($transaction->getItemClicked()->getCustomName() === 'Kit §6Héro') {
                if ($player->hasPermission('arkania:permission.kit.hero')){
                    $player->removeCurrentWindow();
                    $kits->sendKitHero($player, $isAdmin);
                }else{
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit;");
                }
            }elseif($transaction->getItemClicked()->getCustomName() === 'Kit §4Seigneur') {
                if ($player->hasPermission('arkania:permission.kit.seigneur')){
                    $player->removeCurrentWindow();
                    $kits->sendKitSeigneur($player, $isAdmin);
                }else{
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit;");
                }
            }
            return $transaction->discard();
        });
        $menu->send($player);
    }

    /**
     * @param Player $player
     * @param bool|string $faction
     * @return void
     */
    public function sendSettingsFactionForm(Player $player, bool|string $faction): void {
        $form = new SimpleForm(function (Player $player, $data) use ($faction){

            if (is_null($data))
                return;

            if ($data === 0)
                $this->sendDescriptionFactionForm($player, $faction);
            elseif($data === 1)
                $this->sendLogsDiscordForm($player, $faction);
        });
        $form->setTitle('§c- §fSettings §c-');
        $form->setContent("§7» §rVoici les paramètres de faction. Choisissez ce que vous voulez modifer puis cliquez.");
        $form->addButton('§7» §rDescription');
        $form->addButton('§7» §rLogs');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param $faction
     * @return void
     */
    public function sendDescriptionFactionForm(Player $player, $faction): void {
        $form = new CustomForm(function (Player $player, $data) use ($faction){

            if (is_null($data))
                return;

            if (strlen($data[1]) > 50){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez mettre plus de §e50 caractères§c dans la description de votre faction.");
                return;
            }

            $factionManager = new FactionManager();
            $factionManager->getFactionClass($faction, $player->getName())->setDescription($data[1]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien mis à jour la description de votre faction.");
        });
        $form->setTitle('§c- §fDescription §c-');
        $form->setContent("§7» §rMettez la description que vous souhaitez ajouter à votre faction.");
        $form->addInput('§7» §rDescription');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param $faction
     * @return void
     */
    public function sendLogsDiscordForm(Player $player, $faction): void {
        $form = new CustomForm(function (Player $player, $data) use ($faction){
            if (is_null($data))
                return;

            if ((bool)$data[0] === false){
                self::$faction_webhook[$player->getName()] = $player->getName();
                $player->sendMessage(Utils::getPrefix() . "§6Merci de mettre l'url du webhook dans le chat afin d'activer les logs pour votre faction.");
            }else{
                $factionManager = new FactionManager();
                $factionManager->getFactionClass($faction, $player->getName())->setUrl('');
                $player->sendMessage(Utils::getPrefix() . "§cVous venez de désactiver les logs de faction.");
            }
        });
        $form->setTitle('§c- §fLogs §c-');
        $form->addDropdown('§7» §rLogs', ['§aActivé', '§cDésactivé']);
        $player->sendForm($form);
    }
}