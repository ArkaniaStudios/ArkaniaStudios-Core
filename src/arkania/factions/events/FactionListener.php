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

namespace arkania\factions\events;

use arkania\Core;
use arkania\factions\FactionClass;
use arkania\utils\Utils;
use pocketmine\block\Barrel;
use pocketmine\block\Chest;
use pocketmine\block\ShulkerBox;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;

final class FactionListener implements Listener {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canModifyClaim(Player $player): bool {
        $factionName = $this->core->getFactionManager()->getFaction($player->getName());
        $position = $player->getPosition();
        $chunkX = $position->getFloorX() >> Chunk::COORD_BIT_SIZE;
        $chunkZ = $position->getFloorZ() >> Chunk::COORD_BIT_SIZE;
        $world = $player->getWorld()->getFolderName();
        if (isset(FactionClass::$claim[$chunkX.':'.$chunkZ.':'.$world])) {
            if ($factionName === FactionClass::$claim[$chunkX . ':' . $chunkZ . ':' . $world]['faction'] || $player->hasPermission('arkania:permission.faction.admin'))
                return true;
        }else
            return true;
        $player->sendMessage(Utils::getPrefix() . "§cLe chunk est actuellement claim par la faction §e" . FactionClass::$claim[$chunkX . ':' . $chunkZ . ':' . $world]['faction'] . "§c.");
        return false;
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->canModifyClaim($player)) $event->cancel();
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->canModifyClaim($player)) $event->cancel();
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $blocs = $event->getBlock();

        if ($blocs instanceof Chest or $blocs instanceof ShulkerBox or $blocs instanceof Barrel){
            if (!$this->canModifyClaim($player)) $event->cancel();
        }
    }
}