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

    /** @var callable|null */
    private $completion;

    /**
     * @param string $text
     * @param callable|null $completion
     */
    public function __construct(string $text, callable $completion = null) {
        $this->text = $text;
        $this->completion = $completion;
    }

    public function onRun(): void {
        $database = new MySQLi(DataBaseConnector::HOST_NAME, DataBaseConnector::USER_NAME, DataBaseConnector::PASSWORD, DataBaseConnector::DATABASE);
        $db = $database->prepare($this->text);
        $database->query($this->text);
        if ($database->error) throw new DataBaseException('DataBaseError: ' . $database->error);
        $result = '';
        if ($this->completion !== null)
            $result = $db->get_result();
        $this->setResult($result);
        $database->close();
    }

    public function onCompletion(): void {
        call_user_func($this->completion, $this->getResult());
    }
}