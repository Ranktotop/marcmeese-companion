<?php
/**
 * Lädt die Handschrift "Caveat" lokal aus dem Plugin (assets/fonts/).
 *
 * Divi fordert Caveat nicht auf jeder Seite an (z. B. KI-Cockpit), weil die Schrift dort nur per Custom CSS
 * genutzt wird. Ohne diese Einbindung erscheint eine Ersatzschrift. Es werden keine Daten an Google übertragen.
 *
 * Seiten per Filter mmc/caveat_page_ids anpassbar; die Startseite ist immer dabei.
 */

if (!defined('ABSPATH')) {
    exit;
}

class MMC_Caveat
{
    public static function init()
    {
        add_action('wp_head', [__CLASS__, 'output'], 5);
    }

    public static function pageIds()
    {
        // Startseite, KI-Cockpit und die zweite Seite im neuen Design.
        return array_values(array_filter((array) apply_filters('mmc/caveat_page_ids', [56828, 57207, 57369])));
    }

    public static function output()
    {
        if (!is_front_page() && !is_page(self::pageIds())) {
            return;
        }
        $url   = MMC_URL . 'assets/fonts/';
        $latin = $url . 'caveat-latin-400-700.woff2';
        $ext   = $url . 'caveat-latin-ext-400-700.woff2';

        echo '<link rel="preload" href="' . esc_url($latin) . '" as="font" type="font/woff2" crossorigin>' . "\n";
        echo '<style id="mmc-caveat">'
            . "@font-face{font-family:'Caveat';font-style:normal;font-weight:400 700;font-display:swap;src:url(" . esc_url($ext) . ") format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C4,U+2113,U+2C60-2C7F,U+A720-A7FF;}"
            . "@font-face{font-family:'Caveat';font-style:normal;font-weight:400 700;font-display:swap;src:url(" . esc_url($latin) . ") format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}"
            . '</style>' . "\n";
    }
}
