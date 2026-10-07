<?php
/**
 * Plugin Name:       Marc Meese Companion
 * Description:       Technische Ergänzungen für marcmeese.de: korrigiert lokal gespeicherte Google Fonts und lädt die Handschrift Caveat lokal.
 * Version:           1.0.0
 * Requires at least: 6.9
 * Requires PHP:      8.1
 * Author:            Marc Meese
 * Author URI:        https://marcmeese.de
 * Text Domain:       marcmeese-companion
 * Update URI:        https://github.com/Ranktotop/marcmeese-companion
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MMC_VERSION', '1.0.0');
define('MMC_FILE', __FILE__);
define('MMC_DIR', plugin_dir_path(__FILE__));
define('MMC_URL', plugin_dir_url(__FILE__));

require_once MMC_DIR . 'includes/class-font-repair.php';
require_once MMC_DIR . 'includes/class-caveat.php';

MMC_Font_Repair::init();
MMC_Caveat::init();

register_activation_hook(__FILE__, ['MMC_Font_Repair', 'activate']);
register_deactivation_hook(__FILE__, ['MMC_Font_Repair', 'deactivate']);

// Updates über GitHub-Releases (Asset marcmeese-companion.zip).
if (file_exists(MMC_DIR . 'vendor/autoload.php')) {
    require_once MMC_DIR . 'vendor/autoload.php';

    $mmc_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/Ranktotop/marcmeese-companion/',
        __FILE__,
        'marcmeese-companion'
    );
    $mmc_update_checker->getVcsApi()->enableReleaseAssets('/marcmeese-companion\.zip$/');
}
