<?php
/*
Plugin Name: Simple 301 redirects - Bulk Check
Plugin URI:  https://www.puddinq.com/
Description: Upload your CSV and verify every redirect is working
Version:     0.0.3
Tags: simple, 301, redirect, url, seo, bulk, verify
Author:      Stefan Schotvanger
Author URI:  https://www.puddinq.com/wip/stefan-schotvanger/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: simple-bulk-check
*/



//no direct access
if ( ! defined('WPINC')) {
    die;
}

/**
 * constants
 */
define ('SIMPLE_BULK_CHECK_VERSION', 'v0.0.3');
define ('SIMPLE_BULK_CHECK_DIR', __DIR__);
define ('SIMPLE_BULK_CHECK_FILE', __FILE__);
define ('SIMPLE_BULK_CHECK_URL', plugin_dir_url( __FILE__ ));



/**
 * autoloader
 */
require_once SIMPLE_BULK_CHECK_DIR .'/vendor/autoload.php';

use SimpleBulkCheck\Plugin;



/**
 * start plugin
 */
if ( is_admin() ) {
    $plugin = new Plugin();
}