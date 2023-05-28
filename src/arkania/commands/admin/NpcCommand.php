<?php

namespace arkania\commands\admin;

use arkania\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class NpcCommand extends Command
{
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('npc',
            'Npc - ArkaniaStudios',
            '/npc');
        $this->setPermission('arkania:permission.npc');
        $this->core = $core;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player){
            return;
        }

        if(!$this->testPermission($sender)){
            $sender->sendMessage("§cVous n'avez pas la permission d'effectuer cela !");
            return;
        }

        $this->core->getEntityFormManager()->sendEntityForm($sender);
    }
}