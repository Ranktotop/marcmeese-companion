<?php
/**
 * Repariert die Schriftdateien, die das Plugin "Local Google Fonts" anlegt.
 *
 * Bei variablen Schriften (z. B. Caveat, Inter, Work Sans) schreibt das Plugin den Gewichtsbereich mit Leerzeichen
 * in den Dateinamen ("…-400 700.woff2") und setzt die URL ohne Anführungszeichen ins CSS. Der Browser kann diese
 * Pfade nicht laden, die Schrift fällt auf eine Ersatzschrift zurück.
 *
 * Das Modul benennt solche Dateien um (Leerzeichen → Bindestrich) und korrigiert die font.css. Es läuft, sobald
 * eine lokale font.css ausgeliefert wird (also auch direkt nachdem das Plugin sie neu erzeugt hat), zusätzlich
 * täglich per Cron und bei der Aktivierung.
 */

if (!defined('ABSPATH')) {
    exit;
}

class MMC_Font_Repair
{
    const CRON = 'mmc_font_repair_cron';

    public static function init()
    {
        // Nach Local Google Fonts (Priorität 10), damit die font.css schon existiert.
        add_filter('style_loader_src', [__CLASS__, 'checkStylesheet'], 20, 2);
        add_action(self::CRON, [__CLASS__, 'repairAll']);
    }

    public static function activate()
    {
        self::repairAll();
        if (!wp_next_scheduled(self::CRON)) {
            wp_schedule_event(time() + 300, 'daily', self::CRON);
        }
    }

    public static function deactivate()
    {
        wp_clear_scheduled_hook(self::CRON);
    }

    public static function baseDir()
    {
        $upload = wp_upload_dir();

        return trailingslashit($upload['basedir']) . 'fonts';
    }

    public static function checkStylesheet($src, $handle)
    {
        if (!is_string($src) || strpos($src, '/uploads/fonts/') === false || strpos($src, '/font.css') === false) {
            return $src;
        }
        if (!preg_match('#/uploads/fonts/([a-f0-9]{32})/font\.css#', $src, $m)) {
            return $src;
        }
        $dir = trailingslashit(self::baseDir()) . $m[1];
        $css = $dir . '/font.css';
        if (!file_exists($css)) {
            return $src;
        }

        // Pro Dateistand nur einmal prüfen.
        $key  = 'mmc_fr_' . md5($css . filemtime($css));
        if (get_transient($key)) {
            return $src;
        }
        self::repairDir($dir);
        clearstatcache(true, $css);
        set_transient('mmc_fr_' . md5($css . filemtime($css)), 1, WEEK_IN_SECONDS);

        return $src;
    }

    public static function repairAll()
    {
        $base = self::baseDir();
        if (!is_dir($base)) {
            return 0;
        }
        $fixed = 0;
        foreach ((array) glob($base . '/*', GLOB_ONLYDIR) as $dir) {
            $fixed += self::repairDir($dir) ? 1 : 0;
        }

        return $fixed;
    }

    /**
     * Benennt Dateien mit Leerzeichen um und korrigiert die URLs in der font.css.
     * Gibt true zurück, wenn etwas geändert wurde.
     */
    public static function repairDir($dir)
    {
        $changed = false;
        $map     = [];

        foreach ((array) glob($dir . '/* *') as $file) {
            $new = str_replace(' ', '-', basename($file));
            $target = $dir . '/' . $new;
            if (file_exists($target)) {
                // Gleichnamige Datei existiert schon (z. B. nach einem zweiten Lauf): alte Variante entfernen.
                @unlink($file);
            } elseif (!@rename($file, $target)) {
                continue;
            }
            $map[basename($file)] = $new;
            $changed = true;
        }

        $css = $dir . '/font.css';
        if (file_exists($css)) {
            $content  = (string) file_get_contents($css);
            $original = $content;
            foreach ($map as $old => $new) {
                $content = str_replace($old, $new, $content);
            }
            $content = preg_replace_callback('/url\(([^)]*)\)/', function ($m) {
                return 'url(' . str_replace(' ', '-', $m[1]) . ')';
            }, $content);
            if ($content !== $original) {
                file_put_contents($css, $content);
                $changed = true;
            }
        }

        return $changed;
    }
}
