<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 22/04/2019
 * Time: 22:52
 */

namespace SimpleBulkCheck;


class Request
{

    public static function check_url() {

        check_ajax_referer('check_url', 'nonce' );

        $data = array('errors' => array(), 'succes' => array());

        $args = [
            'urls'    =>      array(
                'filter' => FILTER_SANITIZE_URL,
                'flags'  => FILTER_REQUIRE_ARRAY,
            )
        ];
        $urls = filter_input_array(INPUT_POST, $args);



        if (count($urls) != 2 ) {
            wp_json_encode(array('Error', 'Two urls are needed'));
        }

        $ch = curl_init($_POST['urls'][0]);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $output = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
wp_die();

        switch($http_code) {
            case '200':
                echo "you have status {$http_code}\n";
                echo "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}\n";
                break;
            case '301':
            case '302':
                echo "you have status {$http_code}\n";
                $redirect = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
                if ($redirect == $_POST['urls'][1]) {
                    echo "SUCCES: {$_POST['urls'][0]} is redirecting to {$_POST['urls'][1]}\n";
                } else {
                    echo "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}, but to {$redirect}\n";
                }
                break;
            default:
                echo "you have status {$http_code}\n";

                break;
        }
        curl_close($ch);

        wp_die();
    }

    public static function trackAllLocations($newUrl, $currentUrl){
        echo $currentUrl.' ---> '.$newUrl."\r\n";
    }
}