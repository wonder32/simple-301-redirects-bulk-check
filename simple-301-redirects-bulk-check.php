<?php
/*
Plugin Name: Simple 301 redirects - Bulk Check
Plugin URI:  https://www.puddinq.com/
Description: Upload your CSV and verify every redirect is working
Version:     0.0.1
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

define ('SIMPLE_BULK_CHECK_VERSION', 'v0.0.1');
define ('SIMPLE_BULK_CHECK_DIR', __DIR__);
define ('SIMPLE_BULK_CHECK_FILE', __FILE__);
define ('SIMPLE_BULK_CHECK_URL', plugin_dir_url( __FILE__ ));


require plugin_dir_path( __FILE__ ) . 'src/AdminPage.php';
require plugin_dir_path( __FILE__ ) . 'src/Filter.php';
require plugin_dir_path( __FILE__ ) . 'src/Plugin.php';
require plugin_dir_path( __FILE__ ) . 'src/Request.php';


use SimpleBulkCheck\Plugin;


if ( is_admin() ) {
    $plugin = new Plugin();
}