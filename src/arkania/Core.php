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

use arkania\manager\RanksManager;
use arkania\manager\StatsManager;
use arkania\manager\UiManager;
use arkania\utils\Loader;
use arkania\utils\Permissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Core extends PluginBase {

    use SingletonTrait;

    /** @var Config  */
    public Config $config;

    /** @var Config */
    public Config $playertime;

    /** @var array  */
    public array $staff_logs = [];

    /** @var RanksManager  */
    public RanksManager $ranksManager;

    /** @var UiManager  */
    public UiManager $ui;

    /** @var StatsManager */
    public StatsManager $stats;

    protected function onLoad(): void {
        self::setInstance($this);
    }

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
        $this->loadAllConfig();
        $loader = new Loader($this);
        $loader->init();

        $this->ranksManager = new RanksManager();
        $this->ui = new UiManager();
        $this->stats = new StatsManager($this);

        /* Permission */
        foreach (Permissions::$permissions as $permission)
            PermissionManager::getInstance()->addPermission(new Permission($permission));

        if (!$this->ranksManager->existRank('Joueur'))
            $this->ranksManager->addRank('Joueur');

        /* Logger */
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

    protected function loadAllConfig(): void {
        $this->config = $this->getConfig();
        $this->playertime = new Config($this->getDataFolder() . 'stats/player_time.json', Config::JSON);
    }
}