<?php

/*
  Plugin Name: Kwalificatie Dossier2016
  Plugin URI: 
  Description: Toont kwalificatie dossier informatie obv SBB dossiers 2016.
  Author: Eric_Sprangers
  Version: 2.0.0
  Author URI: http://www.casusopmaat.nl
  License: GPL2
 */

/*  Copyright (C) 2016  Eric Sprangers  (email : eric.sprangers@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') or die("No script kiddies please!");

/**
 * qd_toonWerkProces
 *
 * Toont werkproces titel, omchrijving, gedrag en compententies van een profiel werkproces
 *
 * @param array   $xml          de ingelezen xml structuur
 * @param integer $tabel_id     unieke id voor de tabel
 * @param integer $dossier_nr   bevat het dossier nummer
 * @param array   $indexes      bevat de werkprocesnaam, profiel, kerntaak en werkproces nummers
 * return string
 */
function qd_toonWerkProces(&$xml, $tabel_id, $dossier_nr, $indexes) {
    $werkproces_naam = $indexes[0];

    $dossier = $xml->xpath("dossiers/dossier[@nr='$dossier_nr']");
    if (!$dossier) {
        return "<p>dossier nr $dossier_nr niet gevonden!</p>";
    }
    if ($indexes['type'] === 'B') {
        $dossier_titel = $dossier[0]['titel'];

        $kerntaak = $dossier[0]->xpath("basis/basistaken/basiskerntaken/kerntaak[@hoofdstuk='{$indexes['kerntaak']}']");
        if (!$kerntaak) {
            return "<p>kerntaak K{$indexes['kerntaak']} niet gevonden!</p>";
        }
        $werkproces = $kerntaak[0]->xpath("basiswerkprocessen/werkproces[@hoofdstuk='{$indexes['werkproces']}']");
        if (!$werkproces) {
            return "<p>werkproces W{$indexes['werkproces']} niet gevonden!</p>";
        }
    } else {
        $profiel = $dossier[0]->xpath("profielen/profiel[@hoofdstuk='{$indexes['profiel']}']");
        if (!$profiel) {
            return "<p>profiel P{$indexes['profiel']} niet gevonden!</p>";
        }
        $dossier_titel = $profiel[0]['titel'];
        
        $kerntaak = $profiel[0]->xpath("profielkerntaken/kerntaak[@hoofdstuk='{$indexes['kerntaak']}']");
        if (!$kerntaak) {
            return "<p>kerntaak K{$indexes['kerntaak']} niet gevonden!</p>";
        }
        $werkproces = $kerntaak[0]->xpath("profielwerkprocessen/werkproces[@hoofdstuk='{$indexes['werkproces']}']");
        if (!$werkproces) {
            return "<p>werkproces W{$indexes['werkproces']} niet gevonden!</p>";
        }
    }
    $werkproces_titel = $werkproces[0]['titel'];
    $werkproces_omschrijving = $werkproces[0]->werkprocesomschrijving[0]->__toString();
    $werkproces_gedrag_raw = $werkproces[0]->werkprocesgedrag[0]->__toString();
    $werkproces_resultaat = $werkproces[0]->werkprocesresultaat[0]->__toString();

    /* werkprocesgedrag moet geformatteerd worden */
    $werkproces_gedrag = strtok($werkproces_gedrag_raw, ':') . ":<ul>";
    $gedrag = preg_split('/- /', $werkproces_gedrag_raw);
    for ($i = 1; $i < count($gedrag); $i++) {
        $werkproces_gedrag .= "<li>$gedrag[$i]</li>";  
    }
    $werkproces_gedrag .= "</ul>";

    $werkproces_competenties = 'De onderliggende competenties zijn:';
    $separator = ' ';
    foreach ($werkproces[0]->werkprocescompetenties->competentie as $werkproces_competentie) {
        $competentie = $xml->xpath("competenties/competentie[@nr='{$werkproces_competentie['referentie']}']");
        if (!$competentie) {
            return "<p>interne fout in XML, compententie met referentie {$werkproces_competentie['referentie']} niet gevonden </p>";
        }
        $werkproces_competenties .= $separator . $competentie[0]['titel'];
        $separator = ', ';
    }
    return "<a href=\"#TB_inline?inlineId=kwaldoc16_$tabel_id\" class=\"thickbox\" title=\"Kwalificatiedossier $dossier_titel\">Kwalificatiedossier $dossier_titel</a>
        <div id=\"kwaldoc16_$tabel_id\" class=\"kwaldoc_thickbox\">
            <div class=\"kwaldoc_table\">
                <div class=\"kwaldoc_row  kwaldoc_head\">
                    <div class=\"kwaldoc_label\">Werkproces</div>
                    <div class=\"kwaldoc_data\">$werkproces_naam: $werkproces_titel</div>
                </div>
                <div class=\"kwaldoc_row kwaldoc_body\">
                    <div class=\"kwaldoc_label\">Omschrijving</div>
                    <div class=\"kwaldoc_data\">$werkproces_omschrijving</div>
                </div>
                <div class=\"kwaldoc_row kwaldoc_body\">
                    <div class=\"kwaldoc_label\">Resultaat</div>
                    <div class=\"kwaldoc_data\">$werkproces_resultaat</div>
                </div>
                <div class=\"kwaldoc_row kwaldoc_body\">
                    <div class=\"kwaldoc_label\">Gedrag</div>
                    <div class=\"kwaldoc_data\">$werkproces_gedrag</div>
                </div>
                <div class=\"kwaldoc_row kwaldoc_body\">
                    <div class=\"kwaldoc_label\">Competenties</div>
                    <div class=\"kwaldoc_data\">$werkproces_competenties</div>
                </div>
                <div class=\"kwaldoc_row kwaldoc_foot\">
                    <div class=\"kwaldoc_label\">Bron:</div>
                    <div class=\"kwaldoc_data\"><a href=\"http://www.kwalificatiesmbo.nl\">www.kwalificatiesmbo.nl</a></div>
                </div>
            </div>
        </div>";
}

/**
 * qd_showWerkProces, valideert de input en leest de XML in
 *
 * @param string $dossier       bijvoorbeeld 2631 (Maatschappelijke zorg)
 * @param string $werkproces    bijvoorbeeld B1.K1.W1 voor de basis en P1.K1.W1 voor het profiel
 * @return string
 */
function qd_showWerkProces($dossier, $werkproces) {
    static $tabel_id = 0;
    $tabel_id++;

    /* input validatie */
    $dossier_nr = intval($dossier);
    $dossierFiles = glob(__DIR__ . '/' . $dossier_nr . '*.xml');
    if (empty($dossierFiles)) {
        return "<p>Een XML bestand waarvan de filenaam start met dossiernr: $dossier is niet gevonden !</p>";
    }
    $werkproces_naam = strtoupper(trim($werkproces));
    if (preg_match('/(?<type>(P|B))(?<profiel>\d+)\-K(?<kerntaak>\d+)\-W(?<werkproces>\d+)/i', $werkproces_naam, $indexes) === 0) {
        return "<p>De parameter werkproces: $werkproces is onjuist geformatteerd ! Het format moet zijn P1-K1-W1 of B1-K1-W1</p>";
    }

    /* opbouwen popup info */
    $xml = simplexml_load_file($dossierFiles[0]);
    return qd_toonWerkProces($xml, $tabel_id, $dossier_nr, $indexes);
}

/**
 * Plugin code:
 * Registreer het CSS en javascript
 */
add_action('wp_enqueue_scripts', function() {
    wp_register_style('kwaldoc16style', plugins_url('kwalificatie-dossier.css', __FILE__));
    wp_register_script('kwaldoc16script', plugins_url('kwalificatie-dossier.js', __FILE__), ['jquery']);
});

/**
 * Registreer de shortcode en enqueue de style en script bestanden alleen als de shortcode aanwezig is
 */
if (!function_exists ('kwaldoc16')) {
    add_shortcode('kwaldoc16', function ($atts, $content = null) {
        extract(shortcode_atts([ 'dossier' => 'niet ingevuld', 'werkproces' => 'niet ingevuld'], $atts));

        wp_enqueue_style('kwaldoc16style');
        wp_enqueue_script('kwaldoc16script');
        add_thickbox();
        
        return qd_showwerkproces($dossier, $werkproces);
    });
}
