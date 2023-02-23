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

namespace arkania\tasks\async;

use arkania\data\DataBaseConnector;
use arkania\exception\DataBaseException;
use mysqli;
use pocketmine\scheduler\AsyncTask;

class QueryTask extends AsyncTask {

    /** @var string */
    private string $text;

    /**
     * @param string $text
     */
    public function __construct(string $text) {
        $this->text = $text;
    }

    public function onRun(): void {
    }

    public function onCompletion(): void
    {
        $database = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
        $database->query($this->text);
        if ($database->error) throw new DataBaseException('DataBaseError: ' . $database->error);
        $database->close();
    }
}