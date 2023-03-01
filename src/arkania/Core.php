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

namespace arkania;

use arkania\commands\ranks\CraftCommand;
use arkania\inventory\CraftingTableTypeInventory;
use arkania\jobs\JobsManager;
use arkania\libs\customies\block\CustomiesBlockFactory;
use arkania\libs\muqsit\invmenu\InvMenuHandler;
use arkania\manager\EconomyManager;
use arkania\manager\KitsManager;
use arkania\manager\MaintenanceManager;
use arkania\manager\RanksManager;
use arkania\manager\SanctionManager;
use arkania\manager\ServerStatusManager;
use arkania\manager\StaffManager;
use arkania\manager\StatsManager;
use arkania\manager\SynchronisationManager;
use arkania\manager\UiManager;
use arkania\manager\VoteManager;
use arkania\utils\Loader;
use arkania\utils\Utils;
use Closure;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\format\io\WritableWorldProviderManagerEntry;
use ReflectionException;

class Core extends PluginBase {

    use SingletonTrait;

    /** @var Config */
    public Config $config;

    /** @var Config */
    public Config $playertime;

    /** @var array */
    public array $staff_logs = [];

    /** @var RanksManager */
    public RanksManager $ranksManager;

    /** @var UiManager */
    public UiManager $ui;

    /** @var StatsManager */
    public StatsManager $stats;

    /** @var EconomyManager */
    public EconomyManager $economyManager;

    /** @var SynchronisationManager */
    public SynchronisationManager $synchronisation;

    /** @var ServerStatusManager */
    public ServerStatusManager $serverStatus;

    /** @var MaintenanceManager */
    public MaintenanceManager $maintenance;

    /** @var KitsManager */
    public KitsManager $kits;

    /** @var StaffManager */
    public StaffManager $staff;

    /** @var VoteManager */
    public VoteManager $vote;

    /** @var SanctionManager */
    public SanctionManager $sanction;


    protected function onLoad(): void {
        self::setInstance($this);

        foreach (scandir('/home/container/worlds/') as $world){
            if (Server::getInstance()->getWorldManager()->isWorldGenerated($world))
                $this->getServer()->getWorldManager()->loadWorld($world);
        }

        $provider = new WritableWorldProviderManagerEntry(Closure::fromCallable([LevelDB::class, 'isValid']), fn(string $path) => new LevelDB($path), Closure::fromCallable([LevelDB::class, 'generate']));
        $this->getServer()->getWorldManager()->getProviderManager()->addProvider($provider, 'leveldb', true);
        $this->getServer()->getWorldManager()->getProviderManager()->setDefault($provider);
    }

    /**
     * @throws ReflectionException
     */
    protected function onEnable(): void {
        /* Config */
        if (!file_exists($this->getDataFolder() . 'config.yml'))
            $this->saveDefaultConfig();
        if (!file_exists($this->getDataFolder() . 'npc/data.json'))
            $this->saveResource($this->getDataFolder() . 'npc/data.json');

        if (!file_exists($this->getDataFolder() . 'kits/'))
            @mkdir($this->getDataFolder() . 'kits/');
        if (!file_exists($this->getDataFolder() . 'stats/'))
            @mkdir($this->getDataFolder() . 'stats/');

        /* Loader */

        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

        InvMenuHandler::getTypeRegistry()->register(CraftCommand::INV_MENU_TYPE_WORKBENCH, new CraftingTableTypeInventory());

        $this->loadAllConfig();
        $loader = new Loader($this);
        $loader->init();

        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(static function (): void {
            CustomiesBlockFactory::getInstance()->registerCustomRuntimeMappings();
            CustomiesBlockFactory::getInstance()->addWorkerInitHook();
        }), 0);

        $this->ranksManager = new RanksManager();
        $this->ui = new UiManager();
        $this->stats = new StatsManager($this);
        $this->economyManager = new EconomyManager();
        $this->synchronisation = new SynchronisationManager($this);
        $this->serverStatus = new ServerStatusManager();
        $this->maintenance = new MaintenanceManager($this);
        $this->kits = new KitsManager();
        $this->staff = new StaffManager($this);
        $this->vote = new VoteManager($this);
        $this->sanction = new SanctionManager($this);

        /* Ranks */
        if (!$this->ranksManager->existRank('Joueur'))
            $this->ranksManager->addRank('Joueur');

        /* Logger */
        $serverName = Utils::getServerName();
        if ($this->serverStatus->getServerStatus($serverName) !== '§6Maintenance')
            $this->serverStatus->setServerStatus('ouvert');
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
        if ($this->serverStatus->getServerStatus($serverName) !== '§6Maintenance')
            $this->serverStatus->setServerStatus('ferme');

        foreach ($this->getServer()->getOnlinePlayers() as $player){

            if ($this->staff->isInStaffMode($player))
                $this->staff->removeStaffMode($player);

            $this->stats->removeServerConnection($player);
            $player->sendMessage(Utils::getPrefix() . "§cLe serveur vient de redémarrer. Si vous n'avez pas été merci de vous déconnecter et de vous reconnecter au serveur !");
            $this->ranksManager->synchroQuitRank($player);
            $this->synchronisation->registerInv($player);
        }
    }

    /**
     * @return void
     */
    protected function loadAllConfig(): void {
        $this->config = $this->getConfig();
        $this->playertime = new Config($this->getDataFolder() . 'stats/player_time.json', Config::JSON);
    }

    /**
     * @return JobsManager
     */
    public function getJobsManager(): JobsManager {
        return new JobsManager();
    }

}