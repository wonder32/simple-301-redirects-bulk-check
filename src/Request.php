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
            $data['errors']['two_columns'] = __('There should be two columns', 'simple-bulk-check');
            wp_json_encode($data);
        }
        
        $http_request = self::get_url($_POST['urls'][0]);


        switch($http_request['status']) {
            case '200':
                echo "you have status {$http_request['status']}\n";
                echo "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}\n";
                break;
            case '301':
            case '302':
                echo "you have status {$http_request['status']}\n";
                if ($http_request['redirect'] == $_POST['urls'][1]) {
                    echo "SUCCES: {$_POST['urls'][0]} is redirecting to {$_POST['urls'][1]}\n";
                } else {
                    echo "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}, but to {$redirect}\n";
                }
                break;
            default:
                echo "you have status {$http_request['status']}\n";

                break;
        }
        

        wp_die();
    }
    
    public static function get_url($url) {

        $http_request = array();
            
        $ch = curl_init($_POST['urls'][0]);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $http_request[] = curl_exec($ch);

        $http_request['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $http_request['redirect'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL );
        curl_close($ch);
        return $http_request;
    }

    public static function trackAllLocations($newUrl, $currentUrl){
        echo $currentUrl.' ---> '.$newUrl."\r\n";
    }
}