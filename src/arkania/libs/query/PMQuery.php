<?php

declare(strict_types=1);

/**
 *     _             _                      _
 *    / \     _ __  | | __   __ _   _ __   (_)   __ _
 *   / _ \   | '__| | |/ /  / _` | | '_ \  | |  / _` |
 *  / ___ \  | |    |   <  | (_| | | | | | | | | (_| |
 * /_/   \_\ |_|    |_|\_\  \__,_| |_| |_| |_|  \__,_|
 *
 * ArkaniaStudios is a Network with 2 faction servers 2 mining servers and 2 FFA servers.
 * All plugins that are named after him are dedicated to him and therefore under CopyRight.
 *
 * @author: ArkaniaStudios-Team
 * @link: https://github.com/ArkaniaStudios
 */

namespace arkania\libs\query;

use arkania\exception\QueryException;
use function explode;
use function pack;
use function strlen;

class PMQuery {

    /**
     * @param string $host Ip/dns address being queried
     * @param int $port Port on the ip being queried
     * @param int $timeout Seconds before socket times out
     *
     * @return string[]|int[]
     * @throws QueryException
     */
    public static function query(string $host, int $port, int $timeout = 4): array {
        $socket = @fsockopen('udp://'.$host, $port, $errno, $errstr, $timeout);

        if($errno and $socket !== false) {
            fclose($socket);
            throw new QueryException($errstr, $errno);
        }elseif($socket === false) {
            throw new QueryException($errstr, $errno);
        }

        stream_Set_Timeout($socket, $timeout);
        stream_Set_Blocking($socket, true);

        // hardcoded magic https://github.com/facebookarchive/RakNet/blob/1a169895a900c9fc4841c556e16514182b75faf8/Source/RakPeer.cpp#L135
        $OFFLINE_MESSAGE_DATA_ID = pack('c*', 0x00, 0xFF, 0xFF, 0x00, 0xFE, 0xFE, 0xFE, 0xFE, 0xFD, 0xFD, 0xFD, 0xFD, 0x12, 0x34, 0x56, 0x78);
        $command = pack('cQ', 0x01, time()); // DefaultMessageIDTypes::ID_UNCONNECTED_PING + 64bit current time
        $command .= $OFFLINE_MESSAGE_DATA_ID;
        $command .= pack('Q', 2); // 64bit guid
        $length = strlen($command);

        if($length !== fwrite($socket, $command, $length)) {
            throw new QueryException("Failed to write on socket.", E_WARNING);
        }

        $data = fread($socket, 4096);

        fclose($socket);

        if(empty($data)) {
            throw new QueryException("Server failed to respond", E_WARNING);
        }
        if(!str_starts_with($data, "\x1C")) {
            throw new QueryException("First byte is not ID_UNCONNECTED_PONG.", E_WARNING);
        }
        if(substr($data, 17, 16) !== $OFFLINE_MESSAGE_DATA_ID) {
            throw new QueryException("Magic bytes do not match.");
        }

        // TODO: What are the 2 bytes after the magic?
        $data = \substr($data, 35);

        // TODO: If server-name contains a ';' it is not escaped, and will break this parsing
        $data = explode(';', $data);

        return [
            'GameName' => $data[0] ?? null,
            'HostName' => $data[1] ?? null,
            'Protocol' => $data[2] ?? null,
            'Version' => $data[3] ?? null,
            'Players' => $data[4] ?? null,
            'MaxPlayers' => $data[5] ?? null,
            'ServerId' => $data[6] ?? null,
            'Map' => $data[7] ?? null,
            'GameMode' => $data[8] ?? null,
            'NintendoLimited' => $data[9] ?? null,
            'IPv4Port' => $data[10] ?? null,
            'IPv6Port' => $data[11] ?? null,
            'Extra' => $data[12] ?? null, // TODO: What's in this?
        ];
    }
}