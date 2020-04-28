<?php
/**
 * @package Stim\Sinatra;
 * Plugin Name: Sinatra
 * Plugin URI: https://wetail.ru
 * Description: SImple NAtive TRAnslations. Fetches every post or term and uses Yandex or Google API to translate the text to desired language.
 * Version: 0.0.4
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Author: Stim
 * Author URI: https://wetail.ru
 */


/**
 * Changes Log
 * 0.0.4 - rework on Builder compatibility (external class)
 * 0.0.3 - Compatible to Wetail Page Builder
 * 0.0.2 - Google API implemented, API selector
 */

namespace Stim\Sinatra;

defined( 'ABSPATH' ) or die();

/**
 * Plugin constants
 */
define( __NAMESPACE__ . '\ROOT_NAME',   basename( __DIR__ ) );
define( __NAMESPACE__ . '\ROOT_URL',    plugins_url() . '/' . ROOT_NAME );
define( __NAMESPACE__ . '\SLUG',        basename( __DIR__ ) );
define( __NAMESPACE__ . '\PLUGIN_ID',   basename( __DIR__ ) . '/' . basename( __FILE__ ) );
define( __NAMESPACE__ . '\URL',         dirname( plugins_url() ) . '/' . basename( dirname( __DIR__ ) ) . '/' . ROOT_NAME  );
define( __NAMESPACE__ . '\INDEX',       plugin_basename( __FILE__ ) );
const ROOT_FILE     = __FILE__;
const ROOT_PATH     = __DIR__;
const ROOT_TPL_PATH = __DIR__ . '/templates';
const ASSETS_PATH   = __DIR__ . '/assets';
const ASSETS_URL    = URL . '/assets';

/**
 * Init autoloader
 */
require_once 'autoload.php';

/**
 * Load plugin
 */
_self::load();