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

namespace arkania\manager;

use arkania\Core;
use arkania\utils\trait\Date;
use arkania\utils\Utils;
use JsonException;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class KitsManager {
    use Date;

    private static function getKitConfig(string $kitName): Config {
        return new Config(Core::getInstance()->getDataFolder() . 'kits/' . $kitName . '.json', Config::JSON);
    }

    /**
     * @param Player $player
     * @param bool $inAdmin
     * @return void
     * @throws JsonException
     */
    public function sendKitPlayer(Player $player, bool $inAdmin): void {
        $path = self::getKitConfig("Joueur");
        $time = $path->get($player->getName());

        if (!$inAdmin) {
            if ($time - time() <= 0 || !$time) {
                if (count($player->getInventory()->getContents()) > 27-8){
                    $player->sendMessage(Utils::getPrefix() . "Vous n'avez pas assez de place pour récupérer le kit.");
                    return;
                }
                $this->extractedJoueur($player);

                $path->set($player->getName(), time() + 84600);
                $path->save();
            } else {
                self::sendTimeFormat($time, $player);
            }
        }else{
            $this->extractedJoueur($player);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private function extractedJoueur(Player $player): void
    {
        $player->getInventory()->addItem(VanillaItems::IRON_HELMET());
        $player->getInventory()->addItem(VanillaItems::IRON_CHESTPLATE());
        $player->getInventory()->addItem(VanillaItems::IRON_LEGGINGS());
        $player->getInventory()->addItem(VanillaItems::IRON_BOOTS());
        $player->getInventory()->addItem(VanillaItems::IRON_SWORD());
        $player->getInventory()->addItem(VanillaItems::IRON_PICKAXE());
        $player->getInventory()->addItem(VanillaItems::BREAD()->setCount(16));
        $player->getInventory()->addItem(VanillaBlocks::OAK_WOOD()->asItem()->setCount(10));

        $player->sendMessage(Utils::getPrefix() . "Vous avez recu le kit §7Joueur§f.");
    }

    /**
     * @param Player $player
     * @param bool $inAdmin
     * @return void
     * @throws JsonException
     */
    public function sendKitBooster(Player $player, bool $inAdmin): void {
        $path = self::getKitConfig("Booster");
        $time = $path->get($player->getName());

        if (!$inAdmin) {
            if ($time - time() <= 0 || !$time) {
                if (count($player->getInventory()->getContents()) > 27-9){
                    $player->sendMessage(Utils::getPrefix() . "Vous n'avez pas assez de place pour récupérer le kit.");
                    return;
                }
                $this->extractedBooster($player);

                $path->set($player->getName(), time() + 84600);
                $path->save();
            } else {
                self::sendTimeFormat($time, $player);
            }
        }else{
            $this->extractedBooster($player);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private function extractedBooster(Player $player): void
    {
        $player->getInventory()->addItem(VanillaItems::IRON_HELMET());
        $player->getInventory()->addItem(VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1)));
        $player->getInventory()->addItem(VanillaItems::IRON_LEGGINGS());
        $player->getInventory()->addItem(VanillaItems::IRON_BOOTS());
        $player->getInventory()->addItem(VanillaItems::DIAMOND_SWORD());
        $player->getInventory()->addItem(VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 1)));
        $player->getInventory()->addItem(VanillaItems::BREAD()->setCount(16));
        $player->getInventory()->addItem(VanillaItems::GOLDEN_APPLE()->setCount(2));
        $player->getInventory()->addItem(VanillaBlocks::OAK_WOOD()->asItem()->setCount(10));

        $player->sendMessage(Utils::getPrefix() . "Vous avez recu le kit §dBooster§f.");
    }

    /**
     * @param Player $player
     * @param bool $inAdmin
     * @return void
     * @throws JsonException
     */
    public function sendKitNoble(Player $player, bool $inAdmin): void {
        $path = self::getKitConfig("Noble");
        $time = $path->get($player->getName());

        if (!$inAdmin) {
            if ($time - time() <= 0 || !$time) {
                if (count($player->getInventory()->getContents()) > 27-9){
                    $player->sendMessage(Utils::getPrefix() . "Vous n'avez pas assez de place pour récupérer le kit.");
                    return;
                }
                $this->extractedNoble($player);

                $path->set($player->getName(), time() + 84600);
                $path->save();
            } else {
                self::sendTimeFormat($time, $player);
            }
        }else{
            $this->extractedNoble($player);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private function extractedNoble(Player $player): void
    {
        $player->getInventory()->addItem(VanillaItems::IRON_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1)));
        $player->getInventory()->addItem(VanillaItems::IRON_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2)));
        $player->getInventory()->addItem(VanillaItems::BREAD()->setCount(16));
        $player->getInventory()->addItem(VanillaItems::GOLDEN_APPLE()->setCount(4));
        $player->getInventory()->addItem(VanillaBlocks::OAK_WOOD()->asItem()->setCount(10));

        $player->sendMessage(Utils::getPrefix() . "Vous avez recu le kit §3Noble§f.");
    }

    /**
     * @param Player $player
     * @param bool $inAdmin
     * @return void
     * @throws JsonException
     */
    public function sendKitHero(Player $player, bool $inAdmin): void {
        $path = self::getKitConfig("Hero");
        $time = $path->get($player->getName());

        if (!$inAdmin) {
            if ($time - time() <= 0 || !$time) {
                if (count($player->getInventory()->getContents()) > 27-8){
                    $player->sendMessage(Utils::getPrefix() . "Vous n'avez pas assez de place pour récupérer le kit.");
                    return;
                }
                $this->extractedHero($player);

                $path->set($player->getName(), time() + 84600);
                $path->save();
            } else {
                self::sendTimeFormat($time, $player);
            }
        }else{
            $this->extractedHero($player);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private function extractedHero(Player $player): void
    {
        $player->getInventory()->addItem(VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3)));
        $player->getInventory()->addItem(VanillaItems::BREAD()->setCount(16));
        $player->getInventory()->addItem(VanillaBlocks::OAK_WOOD()->asItem()->setCount(10));

        $player->sendMessage(Utils::getPrefix() . "Vous avez recu le kit §6Héro§f.");
    }

    /**
     * @param Player $player
     * @param bool $inAdmin
     * @return void
     * @throws JsonException
     */
    public function sendKitSeigneur(Player $player, bool $inAdmin): void {
        $path = self::getKitConfig("Seigneur");
        $time = $path->get($player->getName());

        if (!$inAdmin) {
            if ($time - time() <= 0 || !$time) {
                if (count($player->getInventory()->getContents()) > 27-9){
                    $player->sendMessage(Utils::getPrefix() . "Vous n'avez pas assez de place pour récupérer le kit.");
                    return;
                }
                $this->extractedSeigneur($player);

                $path->set($player->getName(), time() + 84600);
                $path->save();
            } else {
                self::sendTimeFormat($time, $player);
            }
        }else{
            $this->extractedSeigneur($player);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private function extractedSeigneur(Player $player): void
    {
        $player->getInventory()->addItem(VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2)));
        $player->getInventory()->addItem(VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4)));
        $player->getInventory()->addItem(VanillaItems::BREAD()->setCount(16));
        $player->getInventory()->addItem(VanillaItems::GOLDEN_APPLE()->setCount(8));
        $player->getInventory()->addItem(VanillaBlocks::OAK_WOOD()->asItem()->setCount(10));

        $player->sendMessage(Utils::getPrefix() . "Vous avez recu le kit §4Seigneur§f.");
    }

    /**
     * @param $time
     * @param Player $player
     * @return void
     */
    private function sendTimeFormat($time, Player $player): void {
        $player->sendMessage(Utils::getPrefix() . "§cVous ne pourrez récupérer ce kit que dans " . $this->tempsFormat($time) . ".");
    }

    /**
     * @param Player $player
     * @return void
     * @throws JsonException
     */
    public function resetCooldown(Player $player): void {
        $path = self::getKitConfig('Joueur');
        $path->remove($player->getName());
        $path->save();
        $path = self::getKitConfig('Booster');
        $path->remove($player->getName());
        $path->save();
        $path = self::getKitConfig('Noble');
        $path->remove($player->getName());
        $path->save();
        $path = self::getKitConfig('Hero');
        $path->remove($player->getName());
        $path->save();
        $path = self::getKitConfig('Seigneur');
        $path->remove($player->getName());
        $path->save();
    }

}