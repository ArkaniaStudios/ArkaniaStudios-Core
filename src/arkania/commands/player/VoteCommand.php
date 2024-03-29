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

namespace arkania\commands\player;

use arkania\commands\BaseCommand;
use arkania\Core;
use arkania\manager\RanksManager;
use arkania\tasks\async\VoteTask;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Internet;

final class VoteCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    public function __construct(Core $core) {
        parent::__construct('vote',
        'Permet de voter pour le serveur.',
        '/vote');
        $this->core = $core;
    }

    /**
     * @param CommandSender $player
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $player, string $commandLabel, array $args): bool {
        if (!$player instanceof Player)
            return true;

        $key = $this->core->config->get('vote-key');
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
                        }, function () use ($playerName){
                            if ($p = $this->core->getServer()->getPlayerExact($playerName)) {
                                $money = mt_rand(500, 2500);
                                $this->core->getEconomyManager()->addMoney($p->getName(), $money);
                                $p->getServer()->broadcastMessage(Utils::getPrefix() . RanksManager::getRanksFormatPlayer($p) . " §fvient de voter pour le serveur et a reçu une récompense !");
                                $this->core->getVoteManager()->addVoteParty();
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