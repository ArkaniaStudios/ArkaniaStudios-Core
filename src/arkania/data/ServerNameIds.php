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

namespace arkania\data;

interface ServerNameIds {

    const SERVER_TRANSFERT_NAME = [
        'faction' => [
            1 => 'fac1',
            2 => 'fac2',
            3 => 'fac3'
        ],
        'minage' => [
            1 => 'minage1',
            2 => 'minage2',
            3 => 'minage3',
            4 => 'minage4'
        ],
        'lobby' => [
            1 => 'lobby1',
            2 => 'lobby2'
        ],
        'dev' => 'servertest'
    ];

    public function __toString(): string;

}