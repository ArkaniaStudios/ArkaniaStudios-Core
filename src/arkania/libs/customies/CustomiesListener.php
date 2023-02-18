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

namespace arkania\libs\customies;

use arkania\libs\customies\block\CustomiesBlockFactory;
use arkania\libs\customies\item\CustomiesItemFactory;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;

final class CustomiesListener implements Listener {

    private ?ItemComponentPacket $cachedItemComponentPacket = null;

    /**
     * @var ItemTypeEntry[]
     */
    private array $cachedItemTable = [];

    /**
     * @var BlockPaletteEntry[]
     */
    private array $cachedBlockPalette = [];

    /**
     * @var Experiments
     */
    private Experiments $experiments;

    public function __construct() {
        $this->experiments = new Experiments([
            'data_driven_items' => true
        ], true);
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void {
        foreach($event->getPackets() as $packet){
            if($packet instanceof BiomeDefinitionListPacket) {
                if($this->cachedItemComponentPacket === null) {
                    $this->cachedItemComponentPacket = ItemComponentPacket::create(CustomiesItemFactory::getInstance()->getItemComponentEntries());
                }
                foreach($event->getTargets() as $session){
                    $session->sendDataPacket($this->cachedItemComponentPacket);
                }
            } elseif($packet instanceof StartGamePacket) {
                if(count($this->cachedItemTable) === 0) {
                    $this->cachedItemTable = array_merge($packet->itemTable, CustomiesItemFactory::getInstance()->getItemTableEntries());
                    $this->cachedBlockPalette = CustomiesBlockFactory::getInstance()->getBlockPaletteEntries();
                }
                $packet->levelSettings->experiments = $this->experiments;
                $packet->itemTable = $this->cachedItemTable;
                $packet->blockPalette = $this->cachedBlockPalette;
            } else if($packet instanceof ResourcePackStackPacket) {
                $packet->experiments = $this->experiments;
            }
        }
    }

}