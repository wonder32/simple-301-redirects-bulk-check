<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 20/04/2019
 * Time: 14:01
 */

namespace SimpleBulkCheck;

//use SimpleBulkCheck\AdminPage;


class Plugin
{
    private $filter;

    public function __construct()
    {
        $admin_page = new AdminPage();
    }

}