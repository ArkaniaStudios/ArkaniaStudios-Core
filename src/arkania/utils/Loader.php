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
use arkania\commands\player\MoneyCommand;
use arkania\commands\player\MsgCommand;
use arkania\commands\player\ReplyCommand;
use arkania\commands\player\SettingsCommand;
use arkania\commands\staff\KickCommand;
use arkania\commands\staff\LogsCommand;
use arkania\commands\staff\RedemCommand;
use arkania\Core;
use arkania\entity\base\BaseEntity;
use arkania\entity\entities\VillagerEntity;
use arkania\events\players\PlayerChatEvent;
use arkania\events\players\PlayerJoinEvent;
use arkania\events\players\PlayerQuitEvent;
use arkania\factions\FactionClass;
use arkania\items\ItemIds;
use arkania\items\NoneEnchant;
use arkania\libs\customies\CustomiesListener;
use arkania\libs\customies\item\CustomiesItemFactory;
use arkania\listener\SynchronisationListener;
use arkania\manager\RanksManager;
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
use arkania\items\npc\NpcManagerItem;

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

    private function initUnLoadCommand(): void {
        $unLoadCommand = [
            'kick',
            'about',
            'me',
            'plugins',
            'tell'
        ];

        $commandMap = $this->core->getServer()->getCommandMap();

        foreach ($unLoadCommand as $unCommand)
            $commandMap->unregister($commandMap->getCommand($unCommand));
    }

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

            /* Moderation */
            new KickCommand($this->core),
            new LogsCommand($this->core),
            new RedemCommand($this->core),

            /* Player */
            new DiscordCommand(),
            new SettingsCommand($this->core),
            new MsgCommand($this->core),
            new ReplyCommand($this->core),
            new FactionCommand($this->core),
            new MoneyCommand($this->core),
        ];

        $this->core->getServer()->getCommandMap()->registerAll('Arkania-Commands', $commands);
    }

    private function initEvents(): void {
        $events = [

            new PlayerJoinEvent($this->core),
            new PlayerQuitEvent($this->core),
            new PlayerChatEvent($this->core),

            new SynchronisationListener($this->core),
            new CustomiesListener(),
        ];

        $eventManager = $this->core->getServer()->getPluginManager();

        foreach ($events as $event)
            $eventManager->registerEvents($event, $this->core);
    }

    private function initData(): void {
        RanksManager::init();
        SettingsManager::init();
        StatsManager::init();
        FactionClass::init();
        SynchronisationManager::init();
    }

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