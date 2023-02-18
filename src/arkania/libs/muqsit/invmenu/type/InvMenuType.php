<?php

declare(strict_types=1);

namespace arkania\libs\muqsit\invmenu\type;

use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}