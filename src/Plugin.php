<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 20/04/2019
 * Time: 14:01
 */

namespace SimpleBulkCheck;



class Plugin
{
    private $filter;

    public function __construct()
    {
        $admin_page = new AdminPage();
        $this->check_for_updates();
        $this->filter = new Filter();
        $this->filter->add_action( 'plugins_loaded', $this, 'load_textdomain' );
        $this->filter->run();

    }

    public function load_textdomain() {
        load_plugin_textdomain( 'simple-bulk-check', false, dirname( plugin_basename(SIMPLE_BULK_CHECK_FILE) ) . '/languages' );
    }

    public function check_for_updates()
    {
        // only load file if it has not been loaded
        if (is_admin()) {
            $map_geo_update_checker = \Puc_v4_Factory::buildUpdateChecker(
                'https://plugins.puddinq.com/updates/?action=get_metadata&slug=simple-301-redirects-bulk-check',
                SIMPLE_BULK_CHECK_FILE
            );
        }

    }

}