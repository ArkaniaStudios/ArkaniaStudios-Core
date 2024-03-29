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

namespace arkania\utils;

use arkania\commands\admin\AdminHomeCommand;
use arkania\commands\admin\AdminKitCommand;
use arkania\commands\admin\MaintenanceCommand;
use arkania\commands\admin\money\AddMoneyCommand;
use arkania\commands\admin\money\DelMoneyCommand;
use arkania\commands\admin\money\ResetMoneyCommand;
use arkania\commands\admin\money\SetMoneyCommand;
use arkania\commands\admin\NpcCommand;
use arkania\commands\admin\ranks\AddPermissionCommand;
use arkania\commands\admin\ranks\AddRankCommand;
use arkania\commands\admin\ranks\AddUPermissionCommand;
use arkania\commands\admin\ranks\DelPermissionCommand;
use arkania\commands\admin\ranks\DelRankCommand;
use arkania\commands\admin\ranks\DelUPermissionCommand;
use arkania\commands\admin\ranks\SetFormatCommand;
use arkania\commands\admin\ranks\SetNametagCommand;
use arkania\commands\admin\ranks\SetRankCommand;
use arkania\commands\admin\SetBoxCommand;
use arkania\commands\admin\SetSpawnCommand;
use arkania\commands\player\BoutiqueCommand;
use arkania\commands\player\CashCommand;
use arkania\commands\player\ClassementCommand;
use arkania\commands\player\CoinsflipCommand;
use arkania\commands\player\CoordinateCommand;
use arkania\commands\player\CreditCommand;
use arkania\commands\player\DelHomeCommand;
use arkania\commands\player\DiscordCommand;
use arkania\commands\player\FactionCommand;
use arkania\commands\player\HomeCommand;
use arkania\commands\player\InfoCommand;
use arkania\commands\player\KitsCommand;
use arkania\commands\player\ListCommand;
use arkania\commands\player\LobbyCommand;
use arkania\commands\player\MoneyCommand;
use arkania\commands\player\MsgCommand;
use arkania\commands\player\PayCommand;
use arkania\commands\player\PingCommand;
use arkania\commands\player\ReplyCommand;
use arkania\commands\player\ScoreBoardCommand;
use arkania\commands\player\ServerInfoCommand;
use arkania\commands\player\ServerSelectorCommand;
use arkania\commands\player\SetHomeCommand;
use arkania\commands\player\SettingsCommand;
use arkania\commands\player\ShopCommand;
use arkania\commands\player\SiteCommand;
use arkania\commands\player\SpawnCommand;
use arkania\commands\player\TpacceptCommand;
use arkania\commands\player\TpaCommand;
use arkania\commands\player\TpaHereCommand;
use arkania\commands\player\TpDenyCommand;
use arkania\commands\player\VoteCommand;
use arkania\commands\player\WarnsCommand;
use arkania\commands\player\WikiCommand;
use arkania\commands\player\XpBottleCommand;
use arkania\commands\ranks\BackCommand;
use arkania\commands\ranks\ClearLagTimeCommand;
use arkania\commands\ranks\CraftCommand;
use arkania\commands\ranks\EnderChestCommand;
use arkania\commands\ranks\FeedCommand;
use arkania\commands\ranks\FurnaceCommand;
use arkania\commands\ranks\NearCommand;
use arkania\commands\ranks\NickCommand;
use arkania\commands\ranks\NightVisionCommand;
use arkania\commands\ranks\RepairCommand;
use arkania\commands\staff\BanListCommand;
use arkania\commands\staff\EnderinvseeCommand;
use arkania\commands\staff\ForceClearLagCommand;
use arkania\commands\staff\InvseeCommand;
use arkania\commands\staff\KickCommand;
use arkania\commands\staff\LogsCommand;
use arkania\commands\staff\MuteCommand;
use arkania\commands\staff\MuteListCommand;
use arkania\commands\staff\RedemCommand;
use arkania\commands\staff\StaffModeCommand;
use arkania\commands\staff\TempsBanCommand;
use arkania\commands\staff\TpWorldCommand;
use arkania\commands\staff\UnBanCommand;
use arkania\commands\staff\UnMuteCommand;
use arkania\commands\staff\WarnCommand;
use arkania\Core;
use arkania\entities\base\CustomEntity;
use arkania\entities\base\SimpleEntity;
use arkania\entities\type\DeathLeaderBoardEntity;
use arkania\entities\type\FloatingText;
use arkania\entities\type\HumanEntity;
use arkania\entities\type\KillLeaderBoardEntity;
use arkania\entities\type\MoneyLeaderBoardEntity;
use arkania\entities\type\VillagerEntity;
use arkania\events\entity\BlockBreakEvent;
use arkania\events\entity\BlockBurnEvent;
use arkania\events\entity\BlockPlaceEvent;
use arkania\events\entity\CommandEvent;
use arkania\events\entity\EntityDamageEntityEvent;
use arkania\events\entity\EntityDamageEvent;
use arkania\events\players\PlayerChatEvent;
use arkania\events\players\PlayerCommandProcessEvent;
use arkania\events\players\PlayerDeathEvent;
use arkania\events\players\PlayerInteractEvent;
use arkania\events\players\PlayerJoinEvent;
use arkania\events\players\PlayerLoginEvent;
use arkania\events\players\PlayerQuitEvent;
use arkania\factions\events\FactionListener;
use arkania\factions\FactionClass;
use arkania\listener\StaffModeListener;
use arkania\listener\SynchronisationListener;
use arkania\manager\EconomyManager;
use arkania\manager\RanksManager;
use arkania\manager\SanctionManager;
use arkania\manager\ServerStatusManager;
use arkania\manager\SettingsManager;
use arkania\manager\StatsManager;
use arkania\manager\SynchronisationManager;
use arkania\tasks\ClearLagTask;
use arkania\tasks\MessageTask;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

final class Loader {

    private Core $core;
    public static array $entities = [];
    private static array $currentName = [];
    private static array $customNamespaces = [];

    public function __construct(Core $core) {
        $this->core = $core;
        $this->initEvents();
        $this->initUnLoadCommand();
        $this->initCommands();
        $this->initData();
        $this->initTask();
        $this->initEntity();
    }

    /**
     * @return void
     */
    private function initUnLoadCommand(): void {
        $unLoadCommand = [
            'kick',
            'about',
            'me',
            'plugins',
            'tell',
            'whitelist',
            'pardon',
            'pardon-ip',
            'banlist',
            'list'
        ];

        $commandMap = $this->core->getServer()->getCommandMap();

        foreach ($unLoadCommand as $unCommand)
            $commandMap->unregister($commandMap->getCommand($unCommand));
    }

    /**
     * @return void
     */
    private function initCommands(): void {
        $commands = [
            /* Administration */
            new NpcCommand($this->core),
            new AddRankCommand($this->core),
            new DelRankCommand($this->core),
            new SetRankCommand($this->core),
            new SetFormatCommand($this->core),
            new SetNametagCommand($this->core),
            new AddPermissionCommand($this->core),
            new DelPermissionCommand($this->core),
            new AddUPermissionCommand($this->core),
            new DelUPermissionCommand($this->core),
            new AddMoneyCommand($this->core),
            new DelMoneyCommand($this->core),
            new SetMoneyCommand($this->core),
            new ResetMoneyCommand($this->core),
            new MaintenanceCommand($this->core),
            new AdminKitCommand($this->core),
            new SetSpawnCommand($this->core),
            new SetBoxCommand($this->core),
            new AdminHomeCommand(),

            /* Moderation */
            new KickCommand($this->core),
            new LogsCommand($this->core),
            new RedemCommand($this->core),
            new StaffModeCommand($this->core),
            new TempsBanCommand($this->core),
            new UnBanCommand($this->core),
            new BanListCommand($this->core),
            new ForceClearLagCommand($this->core),
            new EnderinvseeCommand($this->core),
            new InvseeCommand($this->core),
            new TpWorldCommand($this->core),
            new MuteCommand($this->core),
            new UnMuteCommand($this->core),
            new MuteListCommand($this->core),
            new WarnCommand($this->core),

            /* Grade */
            new FeedCommand(),
            new CraftCommand(),
            new NearCommand(),
            new ClearLagTimeCommand(),
            new NickCommand($this->core),
            new EnderChestCommand(),
            new NightVisionCommand(),
            new BackCommand(),
            new FurnaceCommand(),
            new RepairCommand(),

            /* Player */
            new DiscordCommand(),
            new SettingsCommand($this->core),
            new MsgCommand(),
            new ReplyCommand($this->core),
            new FactionCommand($this->core),
            new MoneyCommand($this->core),
            new ServerSelectorCommand($this->core),
            new PayCommand($this->core),
            new KitsCommand($this->core),
            new VoteCommand($this->core),
            new ServerInfoCommand($this->core),
            new InfoCommand($this->core),
            new SiteCommand(),
            new WikiCommand(),
            new PingCommand($this->core),
            new CoordinateCommand(),
            new ListCommand($this->core),
            new XpBottleCommand(),
            new BoutiqueCommand(),
            new CreditCommand(),
            new SpawnCommand($this->core),
            new LobbyCommand($this->core),
            new TpaCommand($this->core),
            new TpaHereCommand($this->core),
            new TpacceptCommand($this->core),
            new TpDenyCommand($this->core),
            new CashCommand($this->core),
            new CoinsflipCommand($this->core),
            new ClassementCommand($this->core),
            new SetHomeCommand(),
            new DelHomeCommand(),
            new HomeCommand(),
            new WarnsCommand($this->core),
            new ShopCommand($this->core),
            new ScoreBoardCommand(),
            ];

        $this->core->getServer()->getCommandMap()->registerAll('Arkania-Commands', $commands);
    }

    /**
     * @return void
     */
    private function initEvents(): void {
        $events = [

            new PlayerLoginEvent($this->core),
            new PlayerJoinEvent($this->core),
            new PlayerQuitEvent($this->core),
            new PlayerChatEvent($this->core),
            new PlayerInteractEvent($this->core),
            new PlayerDeathEvent(),
            new PlayerCommandProcessEvent(),

            new EntityDamageEntityEvent($this->core),
            new BlockPlaceEvent(),
            new BlockBreakEvent(),
            new EntityDamageEvent(),
            new BlockBurnEvent(),
            new CommandEvent($this->core),

            new SynchronisationListener($this->core),
            new StaffModeListener($this->core),
            new FactionListener($this->core),
        ];

        $eventManager = $this->core->getServer()->getPluginManager();

        foreach ($events as $event)
            $eventManager->registerEvents($event, $this->core);
    }

    /**
     * @return void
     */
    private function initData(): void
    {
        RanksManager::init();
        SettingsManager::init();
        StatsManager::init();
        FactionClass::init();
        SynchronisationManager::init();
        ServerStatusManager::init();
        EconomyManager::init();
        SanctionManager::init();
    }


    /**
     * @return void
     */
    private function initTask(): void {
        $this->core->getScheduler()->scheduleRepeatingTask(new ClearLagTask($this->core, 300), 20);
        $this->core->getScheduler()->scheduleRepeatingTask(new MessageTask($this->core), 9000);
    }

    /**
     * @param string $classEntity
     * @param array $names
     * @param string $currentName
     * @param int|null $entityId
     * @param string|null $customNameSpace
     * @return void
     */
    private function register(string $classEntity, array $names, string $currentName, int $entityId = null, string $customNameSpace = null): void {
        self::$currentName[$currentName] = $currentName;
        foreach ($names as $name){
            self::$entities[strtolower($name)] = $classEntity;
        }
        EntityFactory::getInstance()->register($classEntity, function (World $world, CompoundTag $nbt) use ($classEntity, $names): Entity {
            if($classEntity === HumanEntity::class) {
                return new $classEntity(EntityDataHelper::parseLocation($nbt, $world), HumanEntity::parseSkinNBT($nbt), $nbt);
            }
            return new $classEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $names, $entityId);
        if(!is_null($customNameSpace)){
            self::$customNamespaces[] = $customNameSpace;
        }
    }

    public function initEntity(): void {
        $this->register(HumanEntity::class, ['arkania:human', 'human', 'humain'], 'Humain');
        $this->register(FloatingText::class, ['arkania:floatingtext', 'floatingtext', 'floating text'], 'Floating Text', EntityLegacyIds::FALLING_BLOCK);
        $this->register(VillagerEntity::class, ['arkania:villager', 'villager', 'villageois'], 'Villageois', EntityLegacyIds::VILLAGER);
        $this->register(MoneyLeaderBoardEntity::class, ['arkania:moneyleaderboard', 'leaderboardmoney', 'LeaderBoardMoney'], 'LeaderBoardMoney', EntityLegacyIds::FALLING_BLOCK);
        $this->register(DeathLeaderBoardEntity::class, ['arkania:deathleaderboard', 'leaderboarddeath', 'LeaderBoardDeath'], 'LeaderBoardDeath', EntityLegacyIds::FALLING_BLOCK);
        $this->register(KillLeaderBoardEntity::class, ['arkania:killleaderboard', 'leaderboardkill', 'LeaderBoardKill'], 'LeaderBoardKill', EntityLegacyIds::FALLING_BLOCK);
    }

    /**
     * @param Location $location
     * @param string|int $id
     * @return SimpleEntity|CustomEntity|null
     */
    public static function getEntityById(Location $location, string|int $id): SimpleEntity|CustomEntity|null {
        if(!isset(self::$entities[strtolower($id)])){
            return null;
        }
        return new self::$entities[strtolower($id)]($location);
    }
}