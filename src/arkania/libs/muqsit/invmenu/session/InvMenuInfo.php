<?php

declare(strict_types=1);

namespace arkania\libs\muqsit\invmenu\session;

use arkania\libs\muqsit\invmenu\InvMenu;
use arkania\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic,
		public ?string $graphic_name
	){}
}