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

namespace arkania\events\players;

use arkania\commands\player\FactionCommand;
use arkania\Core;
use arkania\manager\FormManager;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use pocketmine\event\Listener;

final class PlayerChatEvent implements Listener {
    use Date;

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    /**
     * @param \pocketmine\event\player\PlayerChatEvent $event
     * @return void
     */
    public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $factionManager = $this->core->getFactionManager();

        if ($this->core->getSanctionManager()->isMute($player->getName())){

            $sanction = $this->core->getSanctionManager();
            $staff = $sanction->getMuteStaff($player->getName());
            $temps = $sanction->getMuteTime($player->getName());
            $raison = $sanction->getMuteRaison($player->getName());
            $server = $sanction->getMuteServer($player->getName());
            $date = $sanction->getMuteDate($player->getName());

            if ($temps - time() <= 0)
                $sanction->removeMute($player->getName());
            else{
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes actuellement mute." . PHP_EOL . PHP_EOL . '§cStaff: ' . $staff . PHP_EOL . '§cTemps: §e' . $this->tempsFormat($temps) . PHP_EOL . '§cRaison: §e' . $raison . PHP_EOL . '§cServer: §e' . $server . PHP_EOL . '§cDate: §e' . $date);
                $event->cancel();
            }
        }

        if ($this->core->getNickManager()->isNick($player))
            $nick = $this->core->getNickManager()->getNickName($player);
        else
            $nick = null;

        /* Ranks */
        $event->setFormat($this->core->getRanksManager()->getChatFormat($player, $message, $nick));

        if (isset(FormManager::$faction_webhook[$player->getName()])){
            $event->cancel();
            if (!preg_match('#^https://discord\.com/api/webhooks/+#', $message)){
                $player->sendMessage(Utils::getPrefix() . "§cLe liens du webhook n'est pas valide. Les logs de faction ont été automatiquement désactivé.");
                unset(FormManager::$faction_webhook[$player->getName()]);
                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->setLogsStatus(false);
            }else{
                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->setLogsStatus(true);
                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->setUrl($message);
                $player->sendMessage(Utils::getPrefix() . "§aLe webhook a bien été mis en place, les logs de votre faction ont été activés.");
                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - LOGS**', "Les logs de faction ont été activés !");
                unset(FormManager::$faction_webhook[$player->getName()]);
            }
        }

        /* Faction */
        if (isset(FactionCommand::$faction_chat[$player->getName()]) || mb_substr($message, 0, 1) === '!'){

            if ($factionManager->getFaction($player->getName()) === '...')
                return;


            $event->cancel();

            if (mb_substr($message, 0, 1) === '!')
                $factionMessage = mb_substr($message, 1);
            else
                $factionMessage = $message;

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionMessage($factionMessage, $player->getName());
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - CHAT**', $player->getName() . ' » ' . $factionMessage);
        }
    }
}