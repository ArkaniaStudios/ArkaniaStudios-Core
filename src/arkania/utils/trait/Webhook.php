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

namespace arkania\utils\trait;

use arkania\libs\discord\Embed;
use arkania\libs\discord\Message;

trait Webhook {

    /**
     * @param string $title
     * @param string $description
     * @param string $footer
     * @param int $color
     * @param string $url
     * @return void
     */
    public static function sendDiscordWebhook(string $title, string $description, string $footer, int $color, string $url): void {
        $message = new Message();
        $webhook = new \arkania\libs\discord\Webhook($url);
        $embed = new Embed();
        $embed->setTitle($title);
        $embed->setDescription($description);
        $embed->setColor($color);
        $embed->setFooter($footer);
        $embed->setThumbnail("https://cdn.discordapp.com/attachments/977869176007974932/1078714325579075734/alogo.png");
        $message->addEmbed($embed);
        $webhook->send($message);
    }

}