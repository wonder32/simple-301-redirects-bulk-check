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

        $this->filter = new Filter();
        $this->filter->add_action( 'plugins_loaded', $this, 'load_textdomain' );
        $this->filter->run();

        $admin_page = new AdminPage();

    }

    public function load_textdomain() {
        load_plugin_textdomain( 'simple-bulk-check', false, dirname( plugin_basename(SIMPLE_BULK_CHECK_FILE) ) . '/languages' );
    }


}