<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 20/04/2019
 * Time: 23:33
 */

namespace SimpleBulkCheck;


class AdminPage
{

    private $filter;

    public function __construct()
    {
        $this->filter = new Filter();

        $this->filter->add_action('admin_enqueue_scripts', $this, 'enqueue_styles_script');
        $this->filter->add_action('admin_menu', $this, 'create_page');

        $request = new Request();
        $this->filter->add_action('wp_ajax_check_url', $request, 'check_url');
        $this->filter->add_action('wp_ajax_nopriv_check_url', $request, 'check_url');
        $this->filter->run();


    }

    // register menu
    public function create_page()
    {
        add_management_page(
            __('Simple 301 redirects - Bulk Check.', 'simple-bulk-check'),
            __('301 Redirects Verify', 'simple-bulk-check'),
            'manage_options',
            'simple-bulk-check',
            array($this, 'pageOutput') //function
        );


    }

    public function enqueue_styles_script() {

        $value = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce('check_url'),
            'spinner'   => plugins_url('assets/img/spinner.gif', SIMPLE_BULK_CHECK_FILE),
            'file_name' => __('File Name:', 'simple-bulk-check'),
            'succes' => __('SUCCESS', 'simple-bulk-check'),
            'different' => __('redirects different', 'simple-bulk-check'),
            'tem_succes' => __('SUCCESS 302', 'simple-bulk-check'),
            'tem_fail' => __('FAIL 302 to other', 'simple-bulk-check'),
            'fail' => __('FAIL link is not redirecting', 'simple-bulk-check'),
        );

        wp_enqueue_style('simple-bulk-check-style', plugins_url('assets/css/style.css', SIMPLE_BULK_CHECK_FILE), false, SIMPLE_BULK_CHECK_VERSION);
        wp_enqueue_script('simple-bulk-check-script', plugins_url('assets/js/script.js', SIMPLE_BULK_CHECK_FILE), ['jquery'], SIMPLE_BULK_CHECK_VERSION);
        wp_localize_script('simple-bulk-check-script', 'simple_check', $value);
    }

    // page output
    public function pageOutput()
    {
        echo '<div class="wrap simple-bulk-check">';
        echo '<h2>Simple 301 redirects - Bulk Check.</h2>';

        $button = __('Upload csv', 'simple-bulk-check');

        $form = <<<HTML
            <form action="javascript:void(0);" id="the_form">
              <input type="file" id="the_file" required="required" accept=".csv"/>
              <input type="submit" value="$button" class="btn"/>
            </form>
            <div id="file_info"></div>
            <div id="list"></div>
HTML;
        echo $form;

        echo '<div class="legend group-id-1"></div><div class="legend group-id-2"></div><div class="legend group-id-3"></div>' .  __('The same color for the same url', 'simple-bulk-check') ;
        echo '<div class="legend" style="background-color: orange"></div><div class="legend" style="background-color: orange"></div><div class="legend" style="background-color: orange"></div>  ' . __('Highlight the same url everywhere', 'simple-bulk-check') ;



        echo '<table class="widefat" id="simple-bulk-check-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th><input type="checkbox" id="check-all" checked></th></th><th>' . __('Original URL', 'simple-bulk-check') . '</th><th>' . __('Redirect URL', 'simple-bulk-check') . '</th><th id="verifiy-result">' . __('Result', 'simple-bulk-check') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        echo '</thody>';
        echo '</table>';

        echo '</div>';
    }
}