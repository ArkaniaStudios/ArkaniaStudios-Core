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
use arkania\manager\ProtectionManager;
use arkania\utils\trait\Webhook;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\Server;

final class FactionCommand extends BaseCommand {
    use Webhook;

    /** @var Core */
    private Core $core;

    /** @var FactionManager */
    private FactionManager $factionManager;

    /** @var array */
    public array $faction_invite = [];

    /** @var array */
    public static array $faction_chat = [];

    /** @var array */
    public array $faction_allies = [];

    /** @var array */
    public array $cooldown = [];

    public function __construct(Core $core) {
        parent::__construct('faction',
        'Faction - ArkaniaStudios',
        '/faction <argument>',
        ['f']);
        $this->core = $core;
        $this->factionManager = $this->core->getFactionManager();
    }

    public function execute(CommandSender $player, string $commandLabel, array $args): bool {

        if (!$player instanceof Player)
            return true;

        if (count($args) < 1)
            return throw new InvalidCommandSyntaxException();


        $factionManager = $this->factionManager;

        if (strtolower($args[0]) === 'create'){

            if ($factionManager->getFaction($player->getName()) !== '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous êtes déjà dans un faction. Merci de quitter votre faction via la commande §e/f leave §cafin de pouvoir en créer un nouvelle.");
                return true;
            }

            $this->core->getFormManager()->sendCreateFactionForm($player);
        }elseif(strtolower($args[0]) === 'disband'){

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'êtes pas le chef de votre faction. Vous ne pouvez donc pas supprimer cette faction. Si vous voulez créer votre propre faction, faites §e/f create§c.");
                return true;
            }

            if (count($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllies()) > 0){
                foreach ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllies() as $ally) {
                    $factionManager->getFactionClass($ally, $player->getName())->delAllies($factionManager->getFaction($player->getName()));
                }
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - DISBAND**', "La faction vient d'être supprimé par **" . $player->getName() . "**");
            self::sendToastPacket($player, '§7-> §fFACTION', "§cVOUS VENEZ DE SUPPRIMER LA FACTION §e" . $factionManager->getFaction($player->getName()) . " §c!");
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->disbandFaction();
            foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer)
                $this->core->getRanksManager()->updateNameTag($onlinePlayer);
        }elseif(strtolower($args[0]) === 'info'){

            if (!isset($args[1])){
                if ($factionManager->getFaction($player->getName()) === '...'){
                    $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de faction. Faites §e/f info <faction> §cpour avoir les informations d'une faction en question.");
                    return true;
                }
                $this->core->getFormManager()->sendFactionInfoForm($player, $factionManager->getFaction($player->getName()));
            }else{
                if ($factionManager->getFactionClass($args[1], $player->getName())->existFaction())
                    $this->core->getFormManager()->sendFactionInfoForm($player, $args[1]);
                else
                    $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas.");
            }
        }elseif(strtolower($args[0]) === 'leave'){
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
        }elseif(strtolower($args[0]) === 'invite'){

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
        }elseif(strtolower($args[0]) === 'deny'){
            if (!isset($this->faction_invite[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas d'invitation à rejoindre une faction.");
                return true;
            }

            $requester = $this->faction_invite[$player->getName()];
            unset($this->faction_invite[$player->getName()]);

            $player->sendMessage(Utils::getPrefix() . "§cVous venez de refuser l'invitation de faction de la §e" . $factionManager->getFaction($requester) . "§c.");

            if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§c vient de refuser votre invitation de faction.");
        }elseif(strtolower($args[0]) === 'accept'){
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

                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - JOIN**', "**" . $player->getName() . "** vient de rejoindre la faction. Il a été invité par **" . $requester ."**");
                $player->sendMessage(Utils::getPrefix() . "§aVous venez d'accepter l'invitation de faction de la §e" . $factionManager->getFaction($requester) . "§a.");
                $factionManager->getFactionClass($factionManager->getFaction($requester), $requester)->addMember($player);
                $this->core->getRanksManager()->updateNameTag($player);

                if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                    $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§a vient de rejoindre votre invitation de faction.");
            }
        }elseif(strtolower($args[0]) === 'chat'){
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
        }elseif(strtolower($args[0]) === 'kick'){
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

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - KICK**', "**" . $target . "** vient de se faire expulser de la faction par **" . $player->getName() . "**");
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->removeMember($target);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien expulsé le joueur §e". $target . "§a.");

            if (Server::getInstance()->getPlayerExact($target) instanceof Player) {
                Server::getInstance()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . "§cVous avez été expulsé de la §e" . $factionManager->getFaction($player->getName()) . "§a.");
                $this->core->getRanksManager()->updateNameTag(Server::getInstance()->getPlayerExact($target));
            }
        }elseif(strtolower($args[0]) === 'promote'){
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

            if ($target === $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cComme vous êtes le chef de la faction vous ne pouvez pas vous promouvoir officier.");
                return true;
            }

            if ($factionManager->getFaction($target) === '...' || $factionManager->getFaction($target) !== $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez promouvoir cette personne car elle n'est pas dans une faction ou dans votre faction.");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->promoteMember($target);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien promus §e" . $target . "§a en officier de la faction.");

            if (Server::getInstance()->getPlayerExact($target) instanceof Player)
                Server::getInstance()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . "Vous avez été promus officier de la faction §e" . $factionManager->getFaction($player->getName()) . "§f.");
        }elseif(strtolower($args[0]) === 'demote'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de votre faction pour retrograder des gens.");
                return true;
            }

            if (!isset($args[1])){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre le nom de la personne que vous voulez rétrograder.");
                return true;
            }

            $target = $args[1];

            if ($target === $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cComme vous êtes le chef de la faction vous ne pouvez pas vous rétrograder membre.");
                return true;
            }

            if ($factionManager->getFaction($target) === '...' || $factionManager->getFaction($target) !== $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez rétrograder cette personne car elle n'est pas dans une faction ou dans votre faction.");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->demoteMember($target);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien rétrogradé §e" . $target . "§a en membre de la faction.");

            if (Server::getInstance()->getPlayerExact($target) instanceof Player)
                Server::getInstance()->getPlayerExact($target)->sendMessage(Utils::getPrefix() . "Vous avez été rétrogradé membre de la faction §e" . $factionManager->getFaction($player->getName()) . "§f.");
        }elseif(strtolower($args[0]) === 'bank' || strtolower($args[0]) === 'money'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cPour faire ceci il vous faut une faction, faites §e/f create§c pour en créer une.");
                return true;
            }

            $money = $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getMoney();
            $player->sendMessage(Utils::getPrefix() . "Votre faction a actuellement §e" . $money . "§f.");

        }elseif(strtolower($args[0]) === 'debug'){
            if ($player->getName() !== 'Julien8436'){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas exécuter cette commande.");
                return true;
            }

            FactionManager::__debug__('faction');
            $this->sendDiscordWebhook('**FACTION - DEBUG**', '⚠ Julien vient de reset toutes les factions ⚠', '・Plugin faction - ArkaniaStudios', 0xFF3333, 'https://discord.com/api/webhooks/1076778337684439101/MzN86OcFaqQXujJyq3d2tFFblXAEwlR2MsryelOz_jFC-dTjXXNF-sHi3FPB0kGvUPZD');
            $player->sendMessage(Utils::getPrefix() . "§cVous venez de reset toutes les factions !");
            $this->core->getLogger()->warning('/!\ Toutes les factions ont été reset /!\ ');
            $this->core->getServer()->shutdown();
        }elseif(strtolower($args[0]) === 'top') {
            $allFaction = $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllFaction();
            arsort($allFaction);
            $maxpages = intval(abs(count($allFaction) / 10));
            $reste = count($allFaction) % 10;
            if ($reste > 0) {
                $maxpage = $maxpages + 1;
            } else {
                $maxpage = $maxpages;
            }
            if ((isset($args[1])) and (!(is_numeric($args[1])))) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            if (isset($args[1])) $args[1] = intval($args[1]);
            if (!isset($args[1]) or $args[1] == 1) {
                $deptop = 1;
                $fintop = 11;
                $page = 1;
            } else {
                $deptop = (($args[1] - 1) * 10) + 1;
                $fintop = (($args[1] - 1) * 10) + 11;
                $page = $args[1];
            }
            if ($page > $maxpage) {
                $player->sendMessage(Utils::getPrefix() . "§cVeuillez spécifier une page entre §e1 §cet §e$maxpage §c!");
                return true;
            }
            $top = 1;

            $player->sendMessage("§c- §fListe des factions avec le plus de power [§e{$page}§f/§e{$maxpage}§f] §c-");
            $player->sendMessage("\n");
            foreach ($allFaction as $name => $power) {
                if ($top === $fintop) break;
                if ($top >= $deptop) {
                    $player->sendMessage("§6#" . $top . " §l§7» §r§e" . $name . " §favec §e" . $power . " power(s)");
                }
                $top++;
            }
            $player->sendMessage("\n");
        }elseif(strtolower($args[0]) === 'ally'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez avoir une faction pour pouvoir faire ceci. Faites §e/f create §cpour créer votre faction");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être chef de la faction afin de demander une alliance.");
                return true;
            }

            if (count($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllies()) >= 2){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas voir plus de 2 alliances.");
                return true;
            }

            if (!isset($args[1])){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de préciser la nom de la faction avec laquelle vous voulez faire une alliance.");
                return true;
            }

            $faction = $args[1];

            if ($faction === $factionManager->getFaction($player->getName())){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas vous envoyer une demande d'alliance.");
                return true;
            }

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vérifiez si vous avez correctement orthographié le nom de celle-ci.");
                return true;
            }

            $target = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());

            if (!$target instanceof Player){
                $player->sendMessage(Utils::getPrefix() . "§cLe chef de la faction avec laquelle vous souhaitez faire l'alliance n'est pas connecté.");
                return true;
            }

            $player->sendMessage(Utils::getPrefix() . "La demande d'alliance avec la faction §e" . $factionManager->getFaction($target->getName()) . "§f vient d'être envoyé.");
            $target->sendMessage(Utils::getPrefix() . "La faction §e" . $factionManager->getFaction($player->getName()) . "§f souhaiterait faire alliance avec vous:\n\n- §a/f allyok §7-> §fpour accepter\n- §c/f allyno §7-> §fpour refuser.");

            $this->faction_allies[$player->getName()] = $target->getName();
            $this->faction_allies[$target->getName()] = $player->getName();

            $this->cooldown[$target->getName()] = time() + 60*2;
        }elseif(strtolower($args[0]) === 'allyno'){
            if (!isset($this->faction_allies[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de demande d'alliance.");
                return true;
            }

            $requester = $this->faction_allies[$player->getName()];
            unset($this->faction_allies[$player->getName()]);

            $player->sendMessage(Utils::getPrefix() . "§cVous venez de refuser la demande d'alliance avec la faction §e" . $factionManager->getFaction($requester) . "§c.");

            if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§c vient de refuser votre demande d'alliance.");
        }elseif(strtolower($args[0]) === 'allyok'){
            if (!isset($this->faction_allies[$player->getName()])){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de demande d'alliance.");
                return true;
            }

            if (!isset($this->cooldown[$player->getName()]) || $this->cooldown[$player->getName()] - time() <= 0)
                $player->sendMessage(Utils::getPrefix() . "§cLa demande d'alliance a expiré.");
            else {
                if ($factionManager->getFaction($player->getName()) === '...') {
                    $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci.");
                    return true;
                }

                $requester = $this->faction_allies[$player->getName()];
                unset($this->faction_allies[$player->getName()]);

                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->sendFactionLogs('**FACTION - ALLIE**', "Vous êtes maintenant allié avec la faction **" . $factionManager->getFaction($requester) . "**");
                $factionManager->getFactionClass($factionManager->getFaction($requester), $player->getName())->sendFactionLogs('**FACTION - ALLIE**', "Vous êtes maintenant allié avec la faction **" . $factionManager->getFaction($player->getName()) . "**");
                $player->sendMessage(Utils::getPrefix() . "§aVous venez d'accepter la demande d'alliance avec la faction §e" . $factionManager->getFaction($requester) . "§a.");
                $factionManager->getFactionClass($factionManager->getFaction($requester), $requester)->addAllies($factionManager->getFaction($player->getName()));
                $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->addAllies($factionManager->getFaction($requester));
                $this->core->getRanksManager()->updateNameTag($player);

                if ($this->core->getServer()->getPlayerExact($requester) instanceof Player)
                    $this->core->getServer()->getPlayerExact($requester)->sendMessage(Utils::getPrefix() . "§e" . $player->getName() . "§a vient d'accepter votre demande d'alliance.");
            }
        }elseif(strtolower($args[0]) === 'addpower'){
            if (!$player->hasPermission('arkania:permission.faction.addpower')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de faire ceci.");
                return true;
            }

            if (!isset($args[1]) || !isset($args[2])){
                $player->sendMessage('Usage: /f addpower <faction> <power>');
                return true;
            }

            $faction = $args[1];

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vous ne pouvez donc pas lui ajouter du power.");
                return true;
            }

            if (!is_numeric($args[2]) || $args[2] <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
                return true;
            }

            $factionManager->getFactionClass($faction, $player->getName())->addPower((int)$args[2]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien ajouté §e" . $args[2] . " power(s) §aà la faction §e" . $faction . "§a.");
            $owner = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());
            if ($owner instanceof Player)
                $owner->sendMessage(Utils::getPrefix() . "§e" . $args[2] . " power(s) §aont été ajouté à votre faction par un membre du staff.");

            $factionManager->getFactionClass($faction, $player->getName())->sendFactionLogs('**FACTION - POWER**', "・Un membre du staff vient de vous ajouter **" . $args[2] . "** power(s)");
        }elseif(strtolower($args[0]) === 'delpower'){
            if (!$player->hasPermission('arkania:permission.faction.delpower')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de faire ceci.");
                return true;
            }

            if (!isset($args[1]) || !isset($args[2])){
                $player->sendMessage('Usage: /f delpower <faction> <power>');
                return true;
            }

            $faction = $args[1];

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vous ne pouvez donc pas lui ajouter du power.");
                return true;
            }

            if (!is_numeric($args[2]) || $args[2] <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
                return true;
            }

            if ($factionManager->getFactionClass($faction, $player->getName())->getPower() - $args[2] < 0){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas retirer §e" . $args[2] . " power(s) §cà la faction, sinon ses powers seront en négatif. Vous pouvez retirer maximum §e" . $factionManager->getFactionClass($faction, $player->getName())->getPower() . " power(s)§c.");
            }


            $factionManager->getFactionClass($faction, $player->getName())->delPower((int)$args[2]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien retiré §e" . $args[2] . " power(s) §aà la faction §e" . $faction . "§a.");
            $owner = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());
            if ($owner instanceof Player)
                $owner->sendMessage(Utils::getPrefix() . "§e" . $args[2] . " power(s) §aont été retiré(s) à votre faction par un membre du staff.");

            $factionManager->getFactionClass($faction, $player->getName())->sendFactionLogs('**FACTION - POWER**', "・Un membre du staff vient de vous retirer **" . $args[2] . "** power(s)");
        }elseif(strtolower($args[0]) === 'forcedisband'){
            if (!$player->hasPermission('arkania:permission.faction.forcedisband')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de faire ceci.");
                return true;
            }

            if (!isset($args[1])){
                $player->sendMessage('Usage: /f forcedisband <faction>');
                return true;
            }

            $faction = $args[1];

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vous ne pouvez donc pas lui ajouter du power.");
                return true;
            }

            if (count($factionManager->getFactionClass($faction, $player->getName())->getAllies()) > 0){
                foreach ($factionManager->getFactionClass($faction, $player->getName())->getAllies() as $ally) {
                    $factionManager->getFactionClass($ally, $player->getName())->delAllies($factionManager->getFaction($player->getName()));
                }
            }

            $factionManager->getFactionClass($faction, $player->getName())->sendFactionLogs('**FACTION - DISBAND**', "La faction vient d'être supprimé par **" . $this->core->getRanksManager()->getPlayerRank($player->getName()) . "-" . $player->getName() . "** (membre du staff d'arkania)");
            self::sendToastPacket($player, '§7-> §fFACTION', "§cVOUS VENEZ DE SUPPRIMER LA FACTION §e" . $faction . " §c!");
            $factionManager->getFactionClass($faction, $player->getName())->disbandFaction();
            foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer)
                $this->core->getRanksManager()->updateNameTag($onlinePlayer);
        }elseif(strtolower($args[0]) === 'addmoney'){
            if (!$player->hasPermission('arkania:permission.faction.addmoney')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de faire ceci.");
                return true;
            }

            if (!isset($args[1]) || !isset($args[2])){
                $player->sendMessage('Usage: /f addmoney <faction> <money>');
                return true;
            }

            $faction = $args[1];

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vous ne pouvez donc pas lui ajouter du power.");
                return true;
            }

            if (!is_numeric($args[2]) || $args[2] <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
                return true;
            }

            $factionManager->getFactionClass($faction, $player->getName())->addMoney((int)$args[2]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien ajouté §e" . $args[2] . " §aà la faction §e" . $faction . "§a.");
            $owner = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());
            if ($owner instanceof Player)
                $owner->sendMessage(Utils::getPrefix() . "§e" . $args[2] . " §aont été ajouté à votre faction par un membre du staff.");

            $factionManager->getFactionClass($faction, $player->getName())->sendFactionLogs('**FACTION - MONEY**', "・Un membre du staff vient de vous ajouter **" . $args[2] . "** $");
        }elseif(strtolower($args[0]) === 'delmoney'){
            if (!$player->hasPermission('arkania:permission.faction.delmoney')){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas la permission de faire ceci.");
                return true;
            }

            if (!isset($args[1]) || !isset($args[2])){
                $player->sendMessage('Usage: /f delmoney <faction> <power>');
                return true;
            }

            $faction = $args[1];

            if (!$factionManager->getFactionClass($faction, $player->getName())->existFaction()){
                $player->sendMessage(Utils::getPrefix() . "§cCette faction n'existe pas. Vous ne pouvez donc pas lui ajouter du power.");
                return true;
            }

            if (!is_numeric($args[2]) || $args[2] <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de mettre un nombre valide ou supérieur à 0.");
                return true;
            }

            if ($factionManager->getFactionClass($faction, $player->getName())->getMoney() - $args[2] < 0){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas retirer §e" . $args[2] . " §cà la faction, sinon ses powers seront en négatif. Vous pouvez retirer maximum §e" . $factionManager->getFactionClass($faction, $player->getName())->getMoney() . "§c.");
            }


            $factionManager->getFactionClass($faction, $player->getName())->delMoney((int)$args[2]);
            $player->sendMessage(Utils::getPrefix() . "§aVous avez bien retiré §e" . $args[2] . " §aà la faction §e" . $faction . "§a.");
            $owner = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());
            if ($owner instanceof Player)
                $owner->sendMessage(Utils::getPrefix() . "§e" . $args[2] . " §aont été retiré(s) à votre faction par un membre du staff.");

            $factionManager->getFactionClass($faction, $player->getName())->sendFactionLogs('**FACTION - MONEY**', "・Un membre du staff vient de vous retirer **" . $args[2] . "** $");
        }elseif(strtolower($args[0]) === 'allybreak'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()) {
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être chef de la faction pour pouvoir faire ceci.");
                return true;
            }

            $allies = $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getAllies();

            if (!isset($args[1])){
                $player->sendMessage(Utils::getPrefix() . "§cMerci de préciser le nom de la faction avec laquelle vous souhaitez rompre l'alliance.\n\n§7» §cListe des factions avec lesquelles vous êtes allié:\n- §e" . implode("\n§c- §e", $allies));
                return true;
            }

            $faction = $args[1];

            if (!in_array($faction, $allies)){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'êtes pas en alliance avec cette faction.");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->delAllies($faction);
            $factionManager->getFactionClass($faction, $player->getName())->delAllies($factionManager->getFaction($player->getName()));
            $player->sendMessage(Utils::getPrefix() . "§aVous n'êtes plus en alliance avec la faction §e" . $faction . "§a.");
            $target = $this->core->getServer()->getPlayerExact($factionManager->getFactionClass($faction, $player->getName())->getOwner());
            if ($target instanceof Player)
                $target->sendMessage(Utils::getPrefix() . "§aL'alliance avec la faction §e" . $factionManager->getFaction($player->getName()) . "§a vient d'être rompue.");
        }elseif(strtolower($args[0]) === 'settings'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cPour faire ceci vous devez avoir une faction. Faites §e/f create §cpour en créer une.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()) {
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de la faction pour pouvoir faire ceci.");
                return true;
            }

            $this->core->getFormManager()->sendSettingsFactionForm($player, $factionManager->getFaction($player->getName()));
        }elseif(strtolower($args[0]) === 'home') {
            if ($factionManager->getFaction($player->getName()) === '...') {
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci. Faites §e/f create §cpour en créer une.");
                return true;
            }
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->teleportHome($player);
        }elseif(strtolower($args[0]) === 'sethome'){
            if ($factionManager->getFaction($player->getName()) === '...') {
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci. Faites §e/f create §cpour en créer une.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de votre faction pour pouvoir faire ceci.");
                return true;
            }
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->setHome($player);
            $player->sendMessage(Utils::getPrefix() . "§aHome de faction bien définit sur le serveur §e" . Utils::getServerName() . "§a !");
        }elseif(strtolower($args[0]) === 'claim'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci. Faites §e/f create §cpour en créer une.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de votre faction pour pouvoir faire ceci.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->countClaim() >= 2){
                $player->sendMessage(Utils::getPrefix() . "§cVous avez déjà atteins la limite maximal de claim pour une faction.");
                return true;
            }

            if (!ProtectionManager::canModifyZone($player, 'warzone')){
                $player->sendMessage(Utils::getPrefix() . "§cVous ne pouvez pas claim dans le spawn !");
                return true;
            }

            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->addClaim($player);
            $player->sendMessage(Utils::getPrefix() . "§aVous venez de claim le chunk sur lequel vous êtes actuellement.");
        }elseif(strtolower($args[0]) === 'unclaim'){
            if ($factionManager->getFaction($player->getName()) === '...'){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être dans une faction pour pouvoir faire ceci. Faites §e/f create §cpour en créer une.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->getOwner() !== $player->getName()){
                $player->sendMessage(Utils::getPrefix() . "§cVous devez être le chef de votre faction pour pouvoir faire ceci.");
                return true;
            }

            if ($factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->countClaim() <= 0){
                $player->sendMessage(Utils::getPrefix() . "§cVous n'avez pas de claim à unclaim.");
                return true;
            }
            $factionManager->getFactionClass($factionManager->getFaction($player->getName()), $player->getName())->removeClaim($player);
        }
        return true;
    }
}