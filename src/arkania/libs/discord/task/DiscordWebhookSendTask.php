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

namespace arkania\libs\discord\task;

use arkania\Core;
use arkania\libs\discord\Message;
use arkania\libs\discord\Webhook;
use pocketmine\scheduler\AsyncTask;

class DiscordWebhookSendTask extends AsyncTask {

    /** @var Webhook */
    protected Webhook $webhook;
    /** @var Message */
    protected Message $message;

    public function __construct(Webhook $webhook, Message $message){
        $this->webhook = $webhook;
        $this->message = $message;
    }

    public function onRun() : void{
        $ch = curl_init($this->webhook->getURL());
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->message));
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $this->setResult(curl_exec($ch));
        curl_close($ch);
    }

    public function onCompletion() : void{
        $response = $this->getResult();
        if($response !== ""){
            Core::getInstance()->getLogger()->error("[DiscordWebhookAPI] Got error: " . $response);
        }
    }

}