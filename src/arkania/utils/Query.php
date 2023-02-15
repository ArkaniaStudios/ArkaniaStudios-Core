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

namespace arkania\utils;

use arkania\Core;
use arkania\data\DataBaseConnector;
use arkania\exception\DataBaseException;
use mysqli;
use mysqli_result;

class Query {

    public static function query(string $text): mysqli_result|bool
    {
        $database = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
        try {
            $query = $database->query($text);
        }catch (DataBaseException $exception){
            Core::getInstance()->getLogger()->warning('DataBaseError: ' . $exception->getMessage());
        }
        return $query;
    }
}