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

namespace arkania\utils\trait;

use arkania\libs\discord\Embed;
use arkania\libs\discord\Message;

trait Webhook {

    /**
     * @param string $title
     * @param array $description
     * @param string $footer
     * @param int $color
     * @param string $url
     * @return void
     */
    public function sendDiscordWebhook(string $title, array $description, string $footer, int $color, string $url): void {
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