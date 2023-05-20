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

namespace arkania;

use arkania\commands\ranks\CraftCommand;
use arkania\inventory\CraftingTableTypeInventory;
use arkania\jobs\JobsManager;
use arkania\libs\muqsit\invmenu\InvMenuHandler;
use arkania\manager\BoxManager;
use arkania\manager\EconomyManager;
use arkania\manager\FactionManager;
use arkania\manager\FormManager;
use arkania\manager\KitsManager;
use arkania\manager\MaintenanceManager;
use arkania\manager\NickManager;
use arkania\manager\RanksManager;
use arkania\manager\SanctionManager;
use arkania\manager\ServerStatusManager;
use arkania\manager\ShopManager;
use arkania\manager\SpawnManager;
use arkania\manager\StaffManager;
use arkania\manager\StatsManager;
use arkania\manager\SynchronisationManager;
use arkania\manager\TeleportManager;
use arkania\manager\VoteManager;
use arkania\utils\Loader;
use arkania\utils\Utils;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Core extends PluginBase {
    use SingletonTrait;

    /** @var Config */
    public Config $config;

    /** @var array */
    public array $staff_logs = [];

    /** @var RanksManager */
    private RanksManager $ranksManager;

    /** @var FormManager */
    private FormManager $formManager;

    /** @var StatsManager */
    private StatsManager $statsManager;

    /** @var EconomyManager */
    private EconomyManager $economyManager;

    /** @var SynchronisationManager */
    private SynchronisationManager $synchronisationManager;

    /** @var ServerStatusManager */
    public ServerStatusManager $serverStatus;

    /** @var MaintenanceManager */
    private MaintenanceManager $maintenanceManager;

    /** @var KitsManager */
    private KitsManager $kitsManager;

    /** @var StaffManager */
    private StaffManager $staffManager;

    /** @var VoteManager */
    private VoteManager $voteManager;

    /** @var SanctionManager */
    private SanctionManager $sanctionManager;

    /** @var NickManager */
    private NickManager $nickManager;

    /** @var JobsManager */
    private JobsManager $jobsManager;

    /** @var FactionManager */
    private FactionManager $factionManager;

    /** @var SpawnManager */
    private SpawnManager $spawnManager;

    /** @var TeleportManager */
    private TeleportManager $teleportManager;

    /** @var BoxManager */
    private BoxManager $boxManager;

    /** @var ShopManager */
    private ShopManager $shopManager;


    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        /* Config */
        if (!file_exists($this->getDataFolder() . 'config.yml'))
            $this->saveDefaultConfig();
        if (!file_exists($this->getDataFolder() . 'kits/'))
            @mkdir($this->getDataFolder() . 'kits/');
        if (!file_exists($this->getDataFolder() . 'homes/'))
            @mkdir($this->getDataFolder() . 'homes/');

        /* InvMenu */
        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);
        InvMenuHandler::getTypeRegistry()->register(CraftCommand::INV_MENU_TYPE_WORKBENCH, new CraftingTableTypeInventory());

        /* Loader */

        $this->ranksManager = new RanksManager();
        $this->formManager = new FormManager();
        $this->statsManager = new StatsManager();
        $this->economyManager = new EconomyManager();
        $this->synchronisationManager = new SynchronisationManager();
        $this->serverStatusManager = new ServerStatusManager();
        $this->maintenanceManager = new MaintenanceManager($this);
        $this->kitsManager = new KitsManager();
        $this->staffManager = new StaffManager($this);
        $this->voteManager = new VoteManager($this);
        $this->sanctionManager = new SanctionManager();
        $this->nickManager = new NickManager();
        $this->factionManager = new FactionManager();
        $this->spawnManager = new SpawnManager($this);
        $this->teleportManager = new TeleportManager();
        $this->boxManager = new BoxManager($this);
        $this->shopManager = new ShopManager($this);

        new Loader($this);
        $this->getFactionManager()->loadAllConfig();

        /* Ranks */
        if (!$this->ranksManager->existRank('Joueur'))
            $this->ranksManager->addRank('Joueur');

        /* Logger */
        $serverName = Utils::getServerName();
        if ($this->serverStatusManager->getServerStatus($serverName) !== '§6Maintenance')
            $this->serverStatusManager->setServerStatus('ouvert');

        $this->getLogger()->info(
            "\n     _      ____    _  __     _      _   _   ___      _".
            "\n    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \ ".
            "\n   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \ ".
            "\n  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \ ".
            "\n /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\ ".
            "\n ".
            "\n* All data charged."
        );
    }

    /**
     * @return void
     */
    protected function onDisable(): void {
        $serverName = Utils::getServerName();
        if ($this->serverStatusManager->getServerStatus($serverName) !== '§6Maintenance')
            $this->serverStatusManager->setServerStatus('ferme');

        foreach ($this->getServer()->getOnlinePlayers() as $player){
            if ($this->nickManager->isNick($player))
                $this->nickManager->removePlayerNick($player);
            if ($this->staffManager->isInStaffMode($player))
                $this->staffManager->removeStaffMode($player);
            $this->statsManager->removeServerConnection($player);
            $player->sendMessage(Utils::getPrefix() . "§cLe serveur vient de redémarrer. Si vous n'avez pas été merci de vous déconnecter et de vous reconnecter au serveur !");
            $this->ranksManager->synchroQuitRank($player);
            $this->synchronisationManager->registerInv($player);
        }
    }

    /**
     * @return EconomyManager
     */
    public function getEconomyManager(): EconomyManager {
        return $this->economyManager;
    }

    /**
     * @return RanksManager
     */
    public function getRanksManager(): RanksManager {
        return $this->ranksManager;
    }

    /**
     * @return SanctionManager
     */
    public function getSanctionManager(): SanctionManager {
        return $this->sanctionManager;
    }

    /**
     * @return FormManager
     */
    public function getFormManager(): FormManager {
        return $this->formManager;
    }

    /**
     * @return FactionManager
     */
    public function getFactionManager(): FactionManager {
        return $this->factionManager;
    }

    /**
     * @return StaffManager
     */
    public function getStaffManager(): StaffManager {
        return $this->staffManager;
    }

    /**
     * @return KitsManager
     */
    public function getKitsManager(): KitsManager {
        return $this->kitsManager;
    }

    /**
     * @return MaintenanceManager
     */
    public function getMaintenanceManager(): MaintenanceManager {
        return $this->maintenanceManager;
    }

    /**
     * @return NickManager
     */
    public function getNickManager(): NickManager {
        return $this->nickManager;
    }

    /**
     * @return ServerStatusManager
     */
    public function getServerStatus(): ServerStatusManager {
        return $this->serverStatusManager;
    }

    /**
     * @return SynchronisationManager
     */
    public function getSynchronisationManager(): SynchronisationManager {
        return $this->synchronisationManager;
    }

    /**
     * @return VoteManager
     */
    public function getVoteManager(): VoteManager {
        return $this->voteManager;
    }

    /**
     * @return StatsManager
     */
    public function getStatsManager(): StatsManager {
        return $this->statsManager;
    }

    /**
     * @return SpawnManager
     */
    public function getSpawnManager(): SpawnManager {
        return $this->spawnManager;
    }

    /**
     * @return TeleportManager
     */
    public function getTeleportManager(): TeleportManager {
        return $this->teleportManager;
    }

    /**
     * @return BoxManager
     */
    public function getBoxManager(): BoxManager {
        return $this->boxManager;
    }

    /**
     * @return ShopManager
     */
    public function getShopManager(): ShopManager {
        return $this->shopManager;
    }

}
