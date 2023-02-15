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

final class Permissions {

    public static array $permissions = [
        'arkania:permission.npc',
        'arkania:permission.addrank',
        'arkania:permission.delrank',
        'arkania:permission.vanish',
        'arkania:permission.kick',
        'arkania:permission.logs',
        'arkania:permission.redem',
        'arkania:permission.settings.bypass',
        'arkania:permission.setrank',
        'arkania:permission.setformat',
        'arkania:permission.setnametag',
        'arkania:permission.addpermission',
        'arkania:permission.delpermission',
        'arkania:permission.addupermission',
        'arkania:permission.delupermission',
        'arkania:permission.ranks',
        'arkania:permission.listpermission',
        'arkania:permission.listupermission',
    ];

}