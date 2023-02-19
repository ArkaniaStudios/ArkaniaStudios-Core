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

use arkania\libs\customies\block\CustomiesBlockFactory;
use arkania\libs\muqsit\invmenu\InvMenuHandler;
use arkania\manager\EconomyManager;
use arkania\manager\MaintenanceManager;
use arkania\manager\RanksManager;
use arkania\manager\StatsManager;
use arkania\manager\SynchronisationManager;
use arkania\manager\UiManager;
use arkania\utils\Loader;
use arkania\utils\Permissions;
use Closure;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
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

    /** @var MaintenanceManager */
    public MaintenanceManager $maintenance;

    protected function onLoad(): void {
        self::setInstance($this);

        $provider = new WritableWorldProviderManagerEntry(\Closure::fromCallable([LevelDB::class, 'isValid']), fn(string $path) => new LevelDB($path), Closure::fromCallable([LevelDB::class, 'generate']));
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

        if (!file_exists($this->getDataFolder() . 'Kits/'))
            @mkdir($this->getDataFolder() . 'Kits/');
        if (!file_exists($this->getDataFolder() . 'npc/'))
            @mkdir($this->getDataFolder() . 'npc/');
        if (!file_exists($this->getDataFolder() . 'stats/'))
            @mkdir($this->getDataFolder() . 'stats/');

        /* Loader */

        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

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
        $this->maintenance = new MaintenanceManager($this);

        /* Permission */
        foreach (Permissions::$permissions as $permission)
            PermissionManager::getInstance()->addPermission(new Permission($permission));

        /* Ranks */
        if (!$this->ranksManager->existRank('Joueur'))
            $this->ranksManager->addRank('Joueur');

        /* Logger */
        $this->maintenance->setServerStatus('ouvert');
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

    protected function onDisable(): void {

        $this->maintenance->setServerStatus('ferme');

        foreach ($this->getServer()->getOnlinePlayers() as $player){

            $this->ranksManager->synchroQuitRank($player);
            $this->stats->synchroQuitStats($player);

            if ($this->synchronisation->isRegistered($player))
                $this->synchronisation->saveInventory($player);
            $player->removeCurrentWindow();
        }
    }

    protected function loadAllConfig(): void {
        $this->config = $this->getConfig();
        $this->playertime = new Config($this->getDataFolder() . 'stats/player_time.json', Config::JSON);
    }
}