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
 */

namespace arkania\commands\admin\money;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\data\WebhookData;
use arkania\manager\RanksManager;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class DelMoneyCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('delmoney',
            'Delmoney - ArkaniaStudios',
            '/delmoney <player> <amount>');
        $this->setPermission('arkania:permission.delmoney');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
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

        if ($this->core->getEconomyManager()->getMoney($target) - $args[1] < 0){
            $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas supprimer §e" . $args[1] . "§c à §e" . $target . "§c.Vous ne pouvez lui supprimer seulement §e" . $this->core->getEconomyManager()->getMoney($target) . "§c.");
            return true;
        }

        $this->core->getEconomyManager()->delMoney($target, (int)$args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez supprimé §e" . $args[1] . " §fà §e" . $target . "§f.");

        $this->sendDiscordWebhook('**DELMONEY**',"**" . Utils::removeColorOnMessage($rank) . "** vient de supprimer **" . $args[1] . "**$  à **" . $target . "**", 'ArkaniaStudios - Money', 0x05E82E, WebhookData::MONEY);
        $this->sendStaffLogs($rank . ' vient de supprimer ' . $args[1] . ' à ' . $target);

        if ($this->core->getServer()->getPlayerExact($target) instanceof Player)
            $this->core->getServer()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . $rank . " vient de vous supprimer §e" . $args[1] . "§f.");

        return true;
    }

}