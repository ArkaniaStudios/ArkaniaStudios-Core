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

final class SetMoneyCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('setmoney',
            'Setmoney - ArkaniaStudios',
            '/setmoney <player> <amount>');
        $this->setPermission('arkania:permission.setmoney');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        $rank = $player instanceof Player ? RanksManager::getRanksFormatPlayer($player) : '§cAdministrateur §f- §cConsole';

        if (!$this->testPermission($player))
            return true;

        if (count($args) !== 2)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        if (!Utils::isValidNumber($args[1])){
            $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide. Supérieur à 0.");
            return true;
        }

        $this->core->getEconomyManager()->setMoney($target, (int)$args[1]);
        $player->sendMessage(Utils::getPrefix() . "Vous avez définit l'argent de §e" . $target . "§f à §e" . $args[1] . "§f.");

        $this->sendDiscordWebhook('**SETMONEY**',"**" . Utils::removeColorOnMessage($rank) . "** vient de définir l'argent de **" . $target . "** à  **" . $args[1] . "**$", 'ArkaniaStudios - Money', 0x05E82E, WebhookData::MONEY);
        $this->sendStaffLogs($rank . ' vient de définir l\'\argent de ' . $target . ' à ' . $args[1] . '');

        if ($this->core->getServer()->getPlayerExact($target) instanceof Player)
            $this->core->getServer()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . $rank . " vient de vous définir votre argent à §e" . $args[1] . "§f.");

        return true;
    }

}