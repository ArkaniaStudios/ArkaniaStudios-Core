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

final class ResetMoneyCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('resetmoney',
            'Resetmoney - ArkaniaStudios',
            '/resetmoney <player>');
        $this->setPermission('arkania:permission.resetmoney');
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

        if (count($args) !== 1)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        $this->core->getEconomyManager()->resetMoney($target);
        $player->sendMessage(Utils::getPrefix() . "Vous avez reset l'argent de §e" . $target . "§f.");

        $this->sendDiscordWebhook('**RESETMONEY**',"**" . Utils::removeColorOnMessage($rank) . "** vient de reset l'argent de  **" . $target . "**", 'ArkaniaStudios - Money', 0x05E82E, WebhookData::MONEY);
        $this->sendStaffLogs($rank . ' vient de reset l\'\argent de ' . $target);

        if ($this->core->getServer()->getPlayerExact($target) instanceof Player)
            $this->core->getServer()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . $rank . " vient de vous reset votre argent.");

        return true;
    }

}