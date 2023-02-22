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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\manager\RanksManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Internet;
use arkania\tasks\async\VoteTask;

class VoteCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('vote',
        'Vote - ArkaniaStudios',
        '/vote');
        $this->core = $core;
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        $key = $this->core->getConfig()->get('vote-key');
        $playerName = $player->getName();

        $voteAsync = new VoteTask(function (VoteTask $a) use ($key, $playerName){
            $get = Internet::getURL(('https://minecraftpocket-servers.com/api/?object=votes&element=claim&key=') . $key . '&username=' . $playerName);
            $a->setResult($get->getBody());
        }, function (VoteTask $a) use ($playerName, $key){
            if ($p = $this->core->getServer()->getPlayerExact($playerName)){
                switch ($a->getResult()){
                    case "0":
                        $p->sendMessage(Utils::getPrefix() . "§cVous devez voté sur le site avant de recevoir vos récompenses.");
                        break;
                    case "1":
                        $v = new VoteTask(function (VoteTask $a) use ($key, $playerName){
                            $get = Internet::getURL('https://minecraftpocket-servers.com/api/?action=post&object=votes&element=claim&key=' . $key . '&username=' . $playerName);
                            $a->setResult($get->getBody());
                        }, function (VoteTask $a) use ($playerName){
                            if ($p = $this->core->getServer()->getPlayerExact($playerName)) {
                                $money = mt_rand(500, 2500);
                                $this->core->economyManager->addMoney($p->getName(), $money);
                                $p->getServer()->broadcastMessage(Utils::getPrefix() . RanksManager::getRanksFormatPlayer($p) . " §fvient de voter pour le serveur et a reçu une récompense !");
                                $this->core->vote->addVoteParty();
                            }
                        });

                        $this->core->getServer()->getAsyncPool()->submitTask($v);
                        break;

                    case "2":
                        $p->sendMessage(Utils::getPrefix() . "§cVous avez déjà voté aujourd'hui, revenez demain");
                        break;
                }
            }
        });
        $this->core->getServer()->getAsyncPool()->submitTask($voteAsync);
        return true;
    }

}