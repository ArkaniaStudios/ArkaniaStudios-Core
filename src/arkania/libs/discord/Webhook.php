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

namespace arkania\libs\discord;

use arkania\libs\discord\task\DiscordWebhookSendTask;
use pocketmine\Server;

class Webhook {

    /** @var string */
    protected string $url;

    public function __construct(string $url){
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getURL(): string{
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isValid(): bool{
        return filter_var($this->url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @param Message $message
     * @return void
     */
    public function send(Message $message): void{
        Server::getInstance()->getAsyncPool()->submitTask(new DiscordWebhookSendTask($this, $message));
    }

}