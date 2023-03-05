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

trait Date {

    /**
     * @return string
     */
    final public function dateFormat(): string {
        $jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        $mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $num_jour = date('w');
        $jour = $jours[$num_jour];
        $num_mois = date('n') - 1;
        $mois = $mois[$num_mois];
        $annee = date('Y');
        $heure = date('H');
        $minute = date('i');
        return $jour . ' ' . date('d') . ' ' . $mois . ' ' . $annee . ' à ' . (int)$heure + 1 . 'H' . $minute;
    }

    /**
     * @param $temps
     * @return string
     */
    final public function tempsFormat($temps): string {
        $timeRestant = $temps - time();
        $jours = floor(abs($timeRestant / 86400));
        $timeRestant = $timeRestant - ($jours * 86400);
        $heures = floor(abs($timeRestant / 3600));
        $timeRestant = $timeRestant - ($heures * 3600);
        $minutes = floor(abs($timeRestant / 60));
        $secondes = ceil(abs($timeRestant - $minutes * 60));

        if($jours > 0)
            $format = $jours . ' jour(s) et ' .  $heures . ' heure(s)';
        else if($heures > 0)
            $format = $heures . ' heure(s) et ' . $minutes . ' minute(s)';
        else if($minutes > 0)
            $format = $minutes . ' minute(s) et ' . $secondes . ' seconde(s)';
        else
            $format = $secondes . 'seconde(s)';
        return $format;
    }
}