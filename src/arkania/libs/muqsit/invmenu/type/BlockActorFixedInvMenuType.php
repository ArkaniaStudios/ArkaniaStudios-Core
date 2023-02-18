<?php

declare(strict_types=1);

namespace arkania\libs\muqsit\invmenu\type;

use arkania\libs\muqsit\invmenu\inventory\InvMenuInventory;
use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\type\graphic\BlockActorInvMenuGraphic;
use arkania\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use arkania\libs\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use arkania\libs\muqsit\invmenu\type\util\InvMenuTypeHelper;
use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

final class BlockActorFixedInvMenuType implements FixedInvMenuType{

	public function __construct(
		private Block $block,
		private int $size,
		private string $tile_id,
		private ?InvMenuGraphicNetworkTranslator $network_translator = null,
		private int $animation_duration = 0
	){}

	public function getSize() : int{
		return $this->size;
	}

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
		$origin = $player->getPosition()->addVector(InvMenuTypeHelper::getBehindPositionOffset($player))->floor();
		if(!InvMenuTypeHelper::isValidYCoordinate($origin->y)){
			return null;
		}

		return new BlockActorInvMenuGraphic($this->block, $origin, BlockActorInvMenuGraphic::createTile($this->tile_id, $menu->getName()), $this->network_translator, $this->animation_duration);
	}

	public function createInventory() : Inventory{
		return new InvMenuInventory($this->size);
	}
}