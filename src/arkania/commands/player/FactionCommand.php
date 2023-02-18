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
use arkania\manager\FactionManager;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

class FactionCommand extends BaseCommand {

    /** @var Core */
    private Core $core;

    /** @var FactionManager */
    private FactionManager $factionManager;

    /** @var array */
    public array $faction_invite = [];

    /** @var array */
    public static array $faction_chat = [];

    /** @var array */
    public array $cooldown = [];

    public function __construct(Core $core) {
        parent::__construct('faction',
        'Faction - ArkaniaStudios',
        '/faction <argument>',
        ['f']);
        $this->core = $core;
        $this->factionManager = new FactionManager();
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();


        $factionManager = $this->factionManager;

        if ($args[0] === 'create'){

            if ($factionManager->getFaction($player->getName()) !== '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes déjà dans un faction. Merci de quitter votre faction via la commande §e/f leave §cafin de pouvoir en créer un nouvelle.");
                return true;
            }

            $this->core->ui->sendCreateFactionForm($player);
        }elseif($args[0] === 'disband'){

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'êtes pas le chef de votre faction. Vous ne pouvez donc pas supprimer cette faction. Si vous voulez créer votre propre faction, faites §e/f create§c.");
                return true;
            }

            self::sendToastPacket($player, '§7-> §fFACTION', "§cVOUS VENEZ DE SUPPRIMER LA FACTION §e" . $factionManager->getFaction($player->getName()) . " §c!");
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->disbandFaction();
            $this->core->ranksManager->updateNameTag($player);
        }elseif($args[0] === 'info'){

            if (!isset($args[1])){
                if ($factionManager->getFaction($player->getName()) === '...'){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de faction. Faites /f info <faction> pour avoir les informations d'une faction en question.");
                    return true;
                }
                $this->core->ui->sendFactionInfoForm($player, $factionManager->getFaction($player->getName()));
            }else{
                if ($factionManager->getFactionClass($args[1], $player->getName())->existFaction())
                    $this->core->ui->sendFactionInfoForm($player, $args[1]);
                else
                    $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas.");
            }
        }elseif($args[0] === 'leave'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être membre d'une faction pour pouvoir la quitter.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() === $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas quitter votre propre faction. Faites §e/f disband §cafin de pouvoir supprimer votre faction.");
                return true;
            }

            $player->sendMessage(Utils::getPrefix() . "§aVous venez de quitter la faction §e" . $factionManager->getFaction($player->getName()) . "§a.");
            foreach ($this->core->getServer()->getOnlinePlayers() as $factionMembers) {
                if ($factionManager->getFaction($factionMembers->getName()) === $factionManager->getFaction($player->getName()))
                    self::sendToastPacket($factionMembers, "§7-> §fFACTION", "§e" . $player->getName() . " §cvient de quitter la faction");
            }
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->removeMember($player->getName());
        }elseif($args[0] === 'invite'){

            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de faction. Si vous souhaitez en créer une faites §e/f create§c.");
                return true;
            }

            if ($factionManager->getFactionRank($player->getName()) === 'member'){
                $player->sendMessage(Utils::getPrefix() . "§cEn tant que membre, vous ne pouvez pas inviter de gens dans la faction. Si vous souhaitez recruter une personne parlez en à un officier ou au chef de votre faction.");
                return true;
            }

            if (!isset($args[1])){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de préciser le nom du joueur que vous souhaitez inviter.");
                return true;
            }

            $target = $this->core->getServer()->getPlayerExact($args[1]);

            if (!$target instanceof Player){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est actuellement pas connecté.");
                return true;
            }

            if ($factionManager->getFaction($target->getName()) === $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur est déjà dans votre faction.");
                return true;
            }

            if (count($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getMembers()) >= 10){
                $player->sendMessage(Utils::getPrefix() . "§cVous avez atteins le nombre maximal de membre dans une faction qui est de §e10§c.");
                return true;
            }

            $this->faction_invite[$player->getName()] = $target->getName();
            $this->faction_invite[$target->getName()] = $player->getName();
            $this->cooldown[$player->getName()] = time() + 60*2;
            $this->cooldown[$target->getName()] = time() + 60*2;
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien invité le joueur §2" . $target->getName() . "§a dans votre faction.");
            $target->sendMessage(Utils::getPrefix() . "Vous avez reçu une invitation pour rejoindre la faction §e" . $factionManager->getFaction($player->getName()) . "§f:\n- §a/f accept §7-> §fpour accepter\n§f- §c/f deny §7-> §fpour refuser.");
        }elseif($args[0] === 'deny'){
            if (!isset($this->faction_invite[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas d'invitation à rejoindre une faction.");
                return true;
            }

            $requester = $this->faction_invite[$player->getName()];
            unset($this->faction_invite[$player->getName()]);

            $player->sendMessage(Utils::getPrefix() . "§cVous venez de refuser l'invitation de faction de la §e" . $factionManager->getFaction($requester) . "§c.");

            if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§c vient de refuser votre invitation de faction.");
        }elseif($args[0] === 'accept'){
            if (!isset($this->faction_invite[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas d'invitation à rejoindre une faction.");
                return true;
            }

            if (!isset($this->cooldown[$player->getName()]) || $this->cooldown[$player->getName()] - time() <= 0)
                $player->sendMessage(Utils::getPrefix() . "§cLa demande d'invitation a expiré.");
            else {
                if ($factionManager->getFaction($player->getName()) !== '...') {
                    $player->sendMessage(Utils::getPrefix() . "§cVous êtes déjà dans une faction. Merci de quitter votre faction afin de pouvoir en rejoindre une autre.");
                    return true;
                }

                $requester = $this->faction_invite[$player->getName()];
                unset($this->faction_invite[$player->getName()]);

                $player->sendMessage(Utils::getPrefix() . "§aVous venez d'accepter l'invitation de faction de la §e" . $factionManager->getFaction($requester) . "§a.");
                $factionManager->getFactionClass($factionManager->getFaction($requester), $requester)->addMember($player);
                $this->core->ranksManager->updateNameTag($player);

                if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                    $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§a vient de rejoindre votre invitation de faction.");
            }
        }elseif($args[0] === 'chat'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour faire ceci.");
                return true;
            }

            if (isset(self::$faction_chat[$player->getName()])) {
                unset(self::$faction_chat[$player->getName()]);
                $player->sendMessage(Utils::getPrefix() . "§cVous venez de désactiver le chat de faction");
            }else {
                self::$faction_chat[$player->getName()] = $player->getName();
                $player->sendMessage(Utils::getPrefix() . "§aVous venez d'activer le chat de faction");
            }
        }elseif($args[0] === 'kick'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de faction et donc ne pouvez pas expulser une personne.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cPour expulser une personne de la faction, vous devez être chef.");
                return true;
            }

            if (!isset($args[1])) {
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre le nom du joueur que vous souhaitez expulser.");
                return true;
            }

            $target = $args[1];

            if ($factionManager->getFaction($target) !== $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cCe joueur n'est pas dans votre faction. Vous ne pouvez donc pas l'expulser.");
                return true;
            }

            if ($target === $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas vous expulser de votre propre faction.");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->removeMember($target);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien expulsé le joueur §e". $target . "§a.");

            if (Server::getInstance()->getPlayerExact($target) instanceof Player) {
                Server::getInstance()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . "§cVous avez été expulsé de la §e" . $factionManager->getFaction($player->getName()) . "§a.");
                $this->core->ranksManager->updateNameTag(Server::getInstance()->getPlayerExact($target));
            }
        }elseif($args[0] === 'promote'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de votre faction pour promouvoir des gens au poste d'officier.");
                return true;
            }

            if (!isset($args[1])){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre le nom de la personne que vous voulez promouvoir.");
                return true;
            }

            $target = $args[1];

            if ($factionManager->getFaction($target) === '...' || $factionManager->getFaction($target) !== $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez promouvoir cette personne car elle n'est pas dans une faction ou dans votre faction.");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->promoteMember($target);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien promus §e" . $target . "§a en officier de la faction.");

            if (Server::getInstance()->getPlayerExact($target) instanceof Player)
                Server::getInstance()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . "Vous avez été promus officier de la faction §e" . $factionManager->getFaction($player->getName()) . "§f.");
        }
        return true;
    }
}