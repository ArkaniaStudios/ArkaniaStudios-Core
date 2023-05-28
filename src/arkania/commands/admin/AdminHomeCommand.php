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

namespace arkania\commands\admin;

use arkania\commands\BaseCommand;
use arkania\data\WebhookData;
use arkania\manager\HomeManager;
use arkania\manager\RanksManager;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

final class AdminHomeCommand extends BaseCommand {
    use Webhook;

    public function __construct() {
        parent::__construct('adminhome',
        'Permet de se téléporter à une home d\'un joueur',
        '/adminhome <player> <home>');
        $this->setPermission('arkania:permission.adminhome');
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        if (!$this->testPermission($player))
            return true;

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();

        $target = $args[0];

        $homeManager = new HomeManager($player->getName());
        $targetHome = $homeManager->getHomeAdmin($target);

        if (count($targetHome->getAll()) === 0){
            $player->sendMessage(Utils::getPrefix() . "§cLe joueur §e" . $target . "§c n'a pas home.");
            return true;
        }

        if (!isset($args[1])){
            $allHome = $targetHome->getAll();
            $homeList = [];
            foreach ($allHome as $home){
                $homeList[] = $home['name'];
            }
            $player->sendMessage(Utils::getPrefix() . "Voici la liste des homes de §e" . $target . "§f :" . PHP_EOL . "- §e" . implode(PHP_EOL . '§f- §e', $homeList));
            return true;
        }

        $homeName = $args[1];

        if (!$targetHome->exists($homeName)){
            $player->sendMessage(Utils::getPrefix() . "§cCe home n'existe pas.");
            return true;
        }
        $player->teleport($homeManager->teleportAtPlayerHome($target, $homeName));
        $player->sendMessage(Utils::getPrefix() . "§aVous avez été téléporté au home §e" . $homeName . "§a du joueur §e" . $target . "§a.");
        $this->sendDiscordWebhook('**ADMINHOME**',"**" . Utils::removeColorOnMessage(RanksManager::getRanksFormatPlayer($player)) . "** vient de se téléporter à un home de **" . $target . "**" . PHP_EOL . PHP_EOL . "- Nom du home: " . $homeName . PHP_EOL . "- Creation date: " . $targetHome->getNested($homeName . ".creation") . PHP_EOL . "- Serveur: " . Utils::getServerName() . PHP_EOL . "- X: " . $targetHome->getNested($homeName . ".x") . PHP_EOL . "- Y: " . $targetHome->getNested($homeName . ".y") . PHP_EOL . "- Z: " . $targetHome->getNested($homeName . ".z"), "Système de home - ArkaniaStudios", 0xE85F05, WebhookData::HOME);
        return true;
    }
}
