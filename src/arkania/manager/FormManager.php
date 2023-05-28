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

use arkania\commands\BaseCommand;
use arkania\commands\player\ServerSelectorCommand;
use arkania\Core;
use arkania\data\SettingsNameIds;
use arkania\data\WebhookData;
use arkania\libs\form\CustomForm;
use arkania\libs\form\SimpleForm;
use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use arkania\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use arkania\libs\muqsit\invmenu\type\InvMenuTypeIds;
use arkania\tasks\TransfertTask;
use arkania\utils\trait\Date;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\item\ItemFactory;

final class FormManager
{
    use Webhook;
    use Date;

    /** @var FactionManager */
    private FactionManager $factionManager;

    /** @var array */
    public static array $faction_webhook = [];

    public function __construct()
    {
        $this->factionManager = new FactionManager();
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendSettingsForm(Player $player): void
    {

        $settings = new SettingsManager($player);
        if ($settings->getSettings(SettingsNameIds::CLEARLAG) === true)
            $statusC = '§aActivé';
        else
            $statusC = '§cDésactivé';

        if ($settings->getSettings(SettingsNameIds::MESSAGE) === true)
            $statusM = '§aActivé';
        else
            $statusM = '§cDésactivé';

        if ($settings->getSettings(SettingsNameIds::TELEPORT) === true)
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
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $settings = new SettingsManager($player);

            if ($transaction->getItemClicked()->getId() === VanillaItems::CLOCK()->getId()) {
                if ($settings->getSettings(SettingsNameIds::CLEARLAG) === false)
                    $settings->setSettings(SettingsNameIds::CLEARLAG, true);
                else
                    $settings->setSettings(SettingsNameIds::CLEARLAG, false);
                $player->removeCurrentWindow();
            } elseif ($transaction->getItemClicked()->getId() === VanillaItems::PAPER()->getId()) {
                if ($settings->getSettings(SettingsNameIds::MESSAGE) === false)
                    $settings->setSettings(SettingsNameIds::MESSAGE, true);
                else
                    $settings->setSettings(SettingsNameIds::MESSAGE, false);
                $player->removeCurrentWindow();
            } elseif ($transaction->getItemClicked()->getId() === VanillaItems::SNOWBALL()->getId()) {
                if ($settings->getSettings(SettingsNameIds::TELEPORT) === false)
                    $settings->setSettings(SettingsNameIds::TELEPORT, true);
                else
                    $settings->setSettings(SettingsNameIds::TELEPORT, false);
                $player->removeCurrentWindow();
            }
            return $transaction->discard();
        });
        $menu->send($player);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendCreateFactionForm(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data) {

            $factionManager = $this->factionManager;

            if (is_null($data))
                return;

            if (is_null($data[1])) {
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nom pour votre faction");
                return;
            }

            if (!Utils::isValidArgument($data[1])) {
                $player->sendMessage(Utils::getPrefix() . "§cUn argument de votre faction n'est pas valide. Merci de le changer.");
                return;
            }

            if (strlen($data[1]) > 10) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre plus de 10 caractères.");
                return;
            }

            if (str_contains($data[1], '§')) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre des caractères qui à pour but de colorer le nom de votre faction.");
                return;
            }

            if ($factionManager->getFactionClass($data[1], $player->getName())->existFaction()) {
                $player->sendMessage(Utils::getPrefix() . "§cCette faction existe déjà.");
                return;
            }

            $inscription = $this->dateFormat();

            $description = $data[2] ?? 'Aucune';

            if (strlen($description) > 50) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas mettre plus de §e50 caractères §cdans la description de votre faction.");
                return;
            }

            $url = 'unknow';

            if ((bool)$data[3] === false) {
                self::$faction_webhook[$player->getName()] = $player->getName();
                $player->sendMessage(Utils::getPrefix() . "§6Merci de mettre l'url du webhook dans le chat afin d'activer les logs pour votre faction.");
            }

            $factionManager->getFactionClass($data[1], $player->getName(), (bool)$data[3], $inscription, $description, $url)->createFaction();
            BaseCommand::sendToastPacket($player, '§7-> §fFACTION', '§aVOUS VENEZ DE CREER LA FACTION §e' . $data[1] . "§a.");
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§e" . $player->getName() . "§f vient de créer la faction §e" . $data[1] . "§f.");
            Core::getInstance()->getRanksManager()->updateNameTag($player);
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
    public function sendFactionInfoForm(Player $player, string $faction): void
    {
        $form = new SimpleForm(function (Player $player, $data) {

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
    public function sendServerSelectorForm(Player $player): void
    {
        $theta = ItemFactory::getInstance()->get(951, 0, 1);
        $zeta = ItemFactory::getInstance()->get(950, 0, 1);
        $minage = ItemFactory::getInstance()->get(952, 0, 1);
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName('      §c- §fCarte du voyageur §c-');
        $menu->getInventory()->setItem(21, $theta->setCustomName('§cThêta')->setLore(['§7§oClique pour te rendre sur le serveur Thêta !']));
        $menu->getInventory()->setItem(23, $zeta->setCustomName('§cZêta')->setLore(['§7§oClique pour te rendre sur le serveur Zêta !']));
        $menu->getInventory()->setItem(29, $minage->setCustomName('§8Minage #1'));
        $menu->getInventory()->setItem(31, $minage->setCustomName('§8Minage #2'));
        $menu->getInventory()->setItem(33, $minage->setCustomName('§8Minage #3'));
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $scheduler = Core::getInstance()->getScheduler();
            $serverStatus = ServerStatusManager::getInstance();

            if ($transaction->getItemClicked()->getCustomName() === '§cThêta') {
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])) {
                    $player->removeCurrentWindow();
                    $player->sendPopup(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                } else {
                    if ($serverStatus->getServerStatus('Theta') === '§cFermé' || $serverStatus->getServerStatus('Theta') === false) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    } elseif ($serverStatus->getServerStatus('Theta') === '§6Maintenance') {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    } else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('faction', 1, $player), 20);
                        $player->sendPopup(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === '§cZêta') {
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])) {
                    $player->removeCurrentWindow();
                    $player->sendPopup(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                } else {
                    if ($serverStatus->getServerStatus('Zeta') === '§cFermé' || $serverStatus->getServerStatus('Zeta') === false) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    } elseif ($serverStatus->getServerStatus('Zeta') === '§6Maintenance') {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    } else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('faction', 2, $player), 20);
                        $player->sendPopup(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }

                }
            } elseif ($transaction->getItemClicked()->getCustomName() === '§8Minage #1') {
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])) {
                    $player->removeCurrentWindow();
                    $player->sendPopup(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                } else {
                    if ($serverStatus->getServerStatus('Minage1') === '§cFermé' || $serverStatus->getServerStatus('Minage1') === false) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    } elseif ($serverStatus->getServerStatus('Minage1') === '§6Maintenance') {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    } else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 1, $player), 20);
                        $player->sendPopup(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === '§8Minage #2') {
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])) {
                    $player->removeCurrentWindow();
                    $player->sendPopup(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                } else {
                    if ($serverStatus->getServerStatus('Minage2') === '§cFermé' || $serverStatus->getServerStatus('Minage2') === false) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    } elseif ($serverStatus->getServerStatus('Minage2') === '§6Maintenance') {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    } else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 2, $player), 20);
                        $player->sendPopup(Utils::getPrefix() . "§aSauvegarde de vos données...");
                    }
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === '§8Minage #3') {
                if (isset(ServerSelectorCommand::$teleport[$player->getName()])) {
                    $player->removeCurrentWindow();
                    $player->sendPopup(Utils::getPrefix() . "§cVos données sont en cour de sauvegarde. Merci de patienter.");
                } else {
                    if ($serverStatus->getServerStatus('Minage3') === '§cFermé' || $serverStatus->getServerStatus('Minage3') === false) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement fermé. Merci de contacter un membre de l'administration ou d'aller voir dans les annonces du discord.");
                    } elseif ($serverStatus->getServerStatus('Minage3') === '§6Maintenance') {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "§cCe serveur est actuellement en maintenance. Rendez-vous sur le discord pour de plus amples explications.");
                    } else {
                        ServerSelectorCommand::$teleport[$player->getName()] = $player->getName();
                        $player->removeCurrentWindow();
                        $scheduler->scheduleRepeatingTask(new TransfertTask('minage', 3, $player), 20);
                        $player->sendPopup(Utils::getPrefix() . "§aSauvegarde de vos données...");
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
    public function sendKitForm(Player $player, bool $isAdmin = false): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName('               §cKits');
        $menu->getInventory()->setItem(10, VanillaItems::RECORD_WAIT()->setCustomName('Kit §7Joueur'));
        $menu->getInventory()->setItem(11, VanillaItems::RECORD_13()->setCustomName('Kit §dBooster'));
        $menu->getInventory()->setItem(13, VanillaItems::RECORD_STAL()->setCustomName('Kit §eNoble'));
        $menu->getInventory()->setItem(14, VanillaItems::RECORD_WARD()->setCustomName('Kit §6Héro'));
        $menu->getInventory()->setItem(15, VanillaItems::RECORD_MELLOHI()->setCustomName('Kit §4Seigneur'));
        $menu->getInventory()->setItem(16, VanillaItems::RECORD_CAT()->setCustomName('Kit §cVidéaste'));
        $menu->setListener(function (InvMenuTransaction $transaction) use ($isAdmin): InvMenuTransactionResult {

            $player = $transaction->getPlayer();
            $kits = Core::getInstance()->getKitsManager();

            if ($transaction->getItemClicked()->getCustomName() === 'Kit §7Joueur') {
                $player->removeCurrentWindow();
                $kits->sendKitPlayer($player, $isAdmin);
            } elseif ($transaction->getItemClicked()->getCustomName() === 'Kit §dBooster') {
                if ($player->hasPermission('arkania:permission.kit.booster')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitBooster($player, $isAdmin);
                } else {
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit.");
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === 'Kit §eNoble') {
                if ($player->hasPermission('arkania:permission.kit.noble')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitNoble($player, $isAdmin);
                } else {
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit.");
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === 'Kit §6Héro') {
                if ($player->hasPermission('arkania:permission.kit.hero')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitHero($player, $isAdmin);
                } else {
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit.");
                }
            } elseif ($transaction->getItemClicked()->getCustomName() === 'Kit §4Seigneur') {
                if ($player->hasPermission('arkania:permission.kit.seigneur')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitSeigneur($player, $isAdmin);
                } else {
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit.");
                }
            }elseif ($transaction->getItemClicked()->getCustomName() === 'Kit §cVidéaste') {
                if ($player->hasPermission('arkania:permission.kit.videaste')) {
                    $player->removeCurrentWindow();
                    $kits->sendKitSeigneur($player, $isAdmin);
                } else {
                    $player->removeCurrentWindow();
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de prendre ce kit.");
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
    public function sendSettingsFactionForm(Player $player, bool|string $faction): void
    {
        $form = new SimpleForm(function (Player $player, $data) use ($faction) {

            if (is_null($data))
                return;

            if ($data === 0)
                $this->sendDescriptionFactionForm($player, $faction);
            elseif ($data === 1)
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
    public function sendDescriptionFactionForm(Player $player, $faction): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($faction) {

            if (is_null($data))
                return;

            if (strlen($data[1]) > 50) {
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez mettre plus de §e50 caractères§c dans la description de votre faction.");
                return;
            }

            $factionManager = Core::getInstance()->getFactionManager();
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
    public function sendLogsDiscordForm(Player $player, $faction): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($faction) {
            if (is_null($data))
                return;

            if ((bool)$data[0] === false) {
                self::$faction_webhook[$player->getName()] = $player->getName();
                $player->sendMessage(Utils::getPrefix() . "§6Merci de mettre l'url du webhook dans le chat afin d'activer les logs pour votre faction.");
            } else {
                $factionManager = new FactionManager();
                $factionManager->getFactionClass($faction, $player->getName())->setUrl('');
                $player->sendMessage(Utils::getPrefix() . "§cVous venez de désactiver les logs de faction.");
            }
        });
        $form->setTitle('§c- §fLogs §c-');
        $form->addDropdown('§7» §rLogs', ['§aActivé', '§cDésactivé']);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param Player $target
     * @return void
     */
    public function sendBanUiForm(Player $player, Player $target): void
    {
        $form = new CustomForm(function (Player $player, $data) use ($target) {
            if (is_null($data))
                return;

            $temps = 0;
            $format = '0 seconde';
            if ($data[1] !== 0) {
                $temps = time() + ((int)$data[1] * 86400);
                $format = (int)$data[1] . ' jour(s) ';
            } elseif ($data[2] !== 0) {
                $temps = time() + ((int)$data[2] * 3600);
                $format = (int)$data[2] . ' heure(s) ';
            } elseif ($data[3] !== 0) {
                $temps = time() + ((int)$data[3] * 60);
                $format = (int)$data[3] . ' minute(s) ';
            } elseif ($data[4] !== 0) {
                $temps = time() + ((int)$data[4]);
                $format = (int)$data[4] . ' seconde(s) ';
            }

            if (is_null($data[5])) {
                if (!$player->hasPermission('arkania:permission.tempsban.bypass')) {
                    $player->sendMessage(Utils::getPrefix() . "§cVous devez obligatoirement indiquer une raison");
                    return;
                }
                $raison = 'Aucun';
            } else
                $raison = $data[5];

            $rank = RanksManager::getRanksFormatPlayer($player);
            Core::getInstance()->getSanctionManager()->addBan($target->getName(), RanksManager::getRanksFormatPlayer($player), $temps, $raison, Utils::getServerName(), $this->dateFormat());
            Server::getInstance()->broadcastMessage(Utils::getPrefix() . "§e" . $target->getName() . "§c vient de se faire bannir du serveur §cdurant §e" . $format . "§c pour le motif §e" . $raison . "§c !");
            $this->sendDiscordWebhook('**BANNISSEMENT**', '**' . $player->getName() . "** vient de bannir **" . $target->getName() . "** d'arkania." . PHP_EOL . PHP_EOL . "*Informations*" . PHP_EOL . "- Banni par **" . Utils::removeColorOnMessage($rank) . "**" . PHP_EOL . "- Durée : **" . $format . "**" . PHP_EOL . "- Server : **" . Utils::getServerName() . "**" . PHP_EOL . "- Raison : **" . $raison . "**", '・Sanction système - ArkaniaStudios', 0xE70235, WebhookData::BAN);
            $target->disconnect("§7» §cVous avez été banni d'Arkania:\n§7» §cStaff: " . $rank . "\n§7» §cTemps: §e" . $format . "\n§7» §cMotif: §e" . $raison);
        });
        $form->setTitle('§c- §fBan §c-');
        $form->setContent("§7» §rVoici l'interface de bannissement.");
        if (!$player->hasPermission('arkania:permission.tempsban.bypass'))
            $form->addSlider('§7» §rJour(s) :', 0, 30, -1, 0);
        else
            $form->addSlider('§7» §rJour(s) :', 0, 100, -1, 0);
        $form->addSlider('§7» §rHeure(s) :', 0, 24);
        $form->addSlider('§7» §rMinute(s) :', 0, 60);
        $form->addSlider('§7» §rSeconde(s) :', 0, 60);
        $form->addInput('§7» §rRaison :');
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param Player $target
     * @return void
     */
    public function sendEnderInvseeForm(Player $player, Player $target): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName('§c- §fEnderChest de §e' . $target->getName() . ' §c-');
        $menu->getInventory()->setContents($target->getEnderInventory()->getContents());
        $menu->setListener(function (InvMenuTransaction $transaction) use ($target): InvMenuTransactionResult {
            $target->getEnderInventory()->setItem($transaction->getAction()->getSlot(), $transaction->getIn());
            return $transaction->continue();
        });
        $menu->send($player);
    }

    public function sendInvseeForm(Player $player, Player $target): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName('§c- §fInventaire de §e' . $target->getName() . ' §c-');
        foreach ($target->getInventory()->getContents() as $slot => $item)
            $menu->getInventory()->setItem($slot, $item);
        for ($i = 36; $i <= 44; $i++)
            $menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->getInventory()->setItem(45, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->getInventory()->setItem(46, $target->getArmorInventory()->getHelmet());
        $menu->getInventory()->setItem(47, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->getInventory()->setItem(48, $target->getArmorInventory()->getChestplate());
        $menu->getInventory()->setItem(49, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->getInventory()->setItem(50, $target->getArmorInventory()->getLeggings());
        $menu->getInventory()->setItem(51, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->getInventory()->setItem(52, $target->getArmorInventory()->getBoots());
        $menu->getInventory()->setItem(53, VanillaBlocks::STAINED_GLASS_PANE()->asItem()->setCustomName('§cBloqué'));
        $menu->setListener(function (InvMenuTransaction $transaction) use ($target, $menu): InvMenuTransactionResult {
            if ($transaction->getAction()->getSlot() >= 36 && $transaction->getAction()->getSlot() <= 44 || $transaction->getAction()->getSlot() == 45 || $transaction->getAction()->getSlot() == 47 || $transaction->getAction()->getSlot() == 49 || $transaction->getAction()->getSlot() == 51 || $transaction->getAction()->getSlot() == 53)
                return $transaction->discard();
            if ($transaction->getAction()->getSlot() <= 35)
                $target->getInventory()->setItem($transaction->getAction()->getSlot(), $transaction->getIn());
            if ($transaction->getAction()->getSlot() === 46)
                $target->getArmorInventory()->setHelmet($transaction->getIn());
            if ($transaction->getAction()->getSlot() === 48)
                $target->getArmorInventory()->setChestplate($transaction->getIn());
            if ($transaction->getAction()->getSlot() === 50)
                $target->getArmorInventory()->setLeggings($transaction->getIn());
            if ($transaction->getAction()->getSlot() === 52)
                $target->getArmorInventory()->setBoots($transaction->getIn());
            return $transaction->continue();
        });
        $menu->send($player);
    }
}
