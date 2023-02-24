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

namespace arkania\utils;

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
use arkania\commands\player\DiscordCommand;
use arkania\commands\player\FactionCommand;
use arkania\commands\player\KitsCommand;
use arkania\commands\player\MoneyCommand;
use arkania\commands\player\MsgCommand;
use arkania\commands\player\PayCommand;
use arkania\commands\player\ReplyCommand;
use arkania\commands\player\ServerInfoCommand;
use arkania\commands\player\ServerSelectorCommand;
use arkania\commands\player\SettingsCommand;
use arkania\commands\player\VoteCommand;
use arkania\commands\ranks\FeedCommand;
use arkania\commands\staff\KickCommand;
use arkania\commands\staff\LogsCommand;
use arkania\commands\staff\RedemCommand;
use arkania\commands\staff\StaffModeCommand;
use arkania\commands\staff\TempsBanCommand;
use arkania\commands\staff\UnBanCommand;
use arkania\Core;
use arkania\entity\base\BaseEntity;
use arkania\entity\entities\VillagerEntity;
use arkania\events\entity\EntityDamageEntityEvent;
use arkania\events\players\PlayerChatEvent;
use arkania\events\players\PlayerJoinEvent;
use arkania\events\players\PlayerLoginEvent;
use arkania\events\players\PlayerQuitEvent;
use arkania\factions\FactionClass;
use arkania\items\ItemIds;
use arkania\items\NoneEnchant;
use arkania\items\npc\NpcManagerItem;
use arkania\jobs\class\Mineur;
use arkania\libs\customies\CustomiesListener;
use arkania\libs\customies\item\CustomiesItemFactory;
use arkania\listener\SynchronisationListener;
use arkania\manager\EconomyManager;
use arkania\manager\RanksManager;
use arkania\manager\SanctionManager;
use arkania\manager\ServerStatusManager;
use arkania\manager\SettingsManager;
use arkania\manager\StatsManager;
use arkania\manager\SynchronisationManager;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use ReflectionException;

final class Loader {

    private Core $core;
    public static array $entities = [];

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @throws ReflectionException
     */
    public function init(): void {
        $this->initEvents();
        $this->initUnLoadCommand();
        $this->initCommands();
        $this->initData();
        $this->initEntity();
        $this->initTask();
        $this->initItem();
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
            'pardon-ip'
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

            /* Moderation */
            new KickCommand($this->core),
            new LogsCommand($this->core),
            new RedemCommand($this->core),
            new StaffModeCommand($this->core),
            new TempsBanCommand($this->core),
            new UnBanCommand($this->core),

            /* Grade */
            new FeedCommand(),

            /* Player */
            new DiscordCommand(),
            new SettingsCommand($this->core),
            new MsgCommand($this->core),
            new ReplyCommand($this->core),
            new FactionCommand($this->core),
            new MoneyCommand($this->core),
            new ServerSelectorCommand($this->core),
            new PayCommand($this->core),
            new KitsCommand($this->core),
            new VoteCommand($this->core),
            new ServerInfoCommand($this->core),
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

            new EntityDamageEntityEvent(),

            new SynchronisationListener($this->core),
            new CustomiesListener(),
        ];

        $eventManager = $this->core->getServer()->getPluginManager();

        foreach ($events as $event)
            $eventManager->registerEvents($event, $this->core);
    }

    /**
     * @return void
     */
    private function initData(): void {
        RanksManager::init();
        SettingsManager::init();
        StatsManager::init();
        FactionClass::init();
        SynchronisationManager::init();
        ServerStatusManager::init();
        EconomyManager::init();
        SanctionManager::init();
        Mineur::init();
    }

    /**
     * @return void
     */
    private function initTask(): void {

    }

    /**
     * @throws ReflectionException
     */
    private function initItem(): void {
        $items = CustomiesItemFactory::getInstance();

        EnchantmentIdMap::getInstance()->register(-10, new NoneEnchant());

        /* Admin */
        $items->registerItem(NpcManagerItem::class, ItemIds::NPC, 'Npc Manager');

    }

    /**
     * @return void
     */
    private function initEntity(): void {
        $this->register(VillagerEntity::class, ['arkania:npc.villager'], EntityLegacyIds::VILLAGER);
    }



    /**
     * @param string $classEntity
     * @param array $names
     * @param int|null $entityId
     * @return void
     */
    public function register(string $classEntity, array $names, int $entityId = null): void {

        foreach ($names as $name)
            self::$entities[strtolower($name)] = $classEntity;

        EntityFactory::getInstance()->register($classEntity, function (World $world, CompoundTag $nbt) use($classEntity, $names) : Entity {

            return new $classEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $names, $entityId);
    }

    /**
     * @param Location $location
     * @param string|int $id
     * @return BaseEntity|null
     */
    public function getEntityById(Location $location, string|int $id) : BaseEntity|null {
        if(!isset(self::$entities[strtolower($id)]))
            return null;

        return new self::$entities[strtolower($id)]($location);

    }

}