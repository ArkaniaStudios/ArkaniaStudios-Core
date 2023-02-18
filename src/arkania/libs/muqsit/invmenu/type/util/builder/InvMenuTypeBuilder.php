<?php

declare(strict_types=1);

namespace arkania\libs\muqsit\invmenu\type\util\builder;

use arkania\libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}