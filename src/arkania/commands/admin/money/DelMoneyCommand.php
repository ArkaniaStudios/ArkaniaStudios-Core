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

namespace arkania\commands\admin\money;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\data\WebhookData;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

class DelMoneyCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('delmoney',
            'Delmoney - ArkaniaStudios',
            '/delmoney <player> <amount>');
        $this->setPermission('arkania:permission.delmoney');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if ($player instanceof Player)
            $rank = RanksManager::getRanksFormatPlayer($player);
        else
            $rank = '§cAdministrateur §f- §cConsole';

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if (!Utils::isValidNumber($args[1])){
            $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide. Supérieur à 0.");
            return true;
        }

        if ($this->core->economyManager->getMoney($target) - $args[1] < 0){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas supprimer §e" . $args[1] . "§c à §e" . $target . "§c.Vous ne pouvez lui supprimer seulement §e" . $this->core->economyManager->getMoney($target) . "§c.");
        }

        $this->core->economyManager->delMoney($target, (int)$args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez supprimé §e" . $args[1] . " §fà §e" . $target . "§f.");

        Utils::sendDiscordWebhook('**DELMONEY**',"**" . $rank . "** vient de supprimer **" . $args[1] . "**$  à **" . $target . "**", 'ArkaniaStudios - Money', 0x05E82E, WebhookData::MONEY);
        $this->sendStaffLogs($rank . ' vient de supprimer ' . $args[1] . ' à ' . $target);

        if ($this->core->getServer()->getPlayerExact($target) instanceof Player)
            $this->core->getServer()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . $rank . " vient de vous supprimer §e" . $args[1] . "§f.");

        return true;
    }

}