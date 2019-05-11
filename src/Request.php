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
            ),
            'id'    =>  FILTER_VALIDATE_INT
        ];
        $urls = filter_input_array(INPUT_POST, $args);



        if (count($urls['urls']) != 2 ) {
            $data['errors']['two_columns'] = __('There should be two columns', 'simple-bulk-check');
            wp_send_json($data);
        }

        $redirects = array();

        $http_request = self::get_url($_POST['urls'][0]);

        $redirects['redirect'] = $http_request['redirect'];
        $redirects['status'] = $http_request['status'];
        $redirects['id'] = $urls['id'];

        switch($http_request['status']) {
            case '200':
                $redirects['message'] = "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}\n";
                break;
            case '301':
            case '302':
                if ($http_request['redirect'] == $_POST['urls'][1]) {
                    $redirects['message'] = "SUCCES: {$_POST['urls'][0]} is redirecting to {$_POST['urls'][1]}\n";
                } else {
                    $redirects['message'] = "FAIL: {$_POST['urls'][0]} is NOT redirecting to {$_POST['urls'][1]}\n";
                }
                break;
            default:
                $redirects['message'] = "you have status {$http_request['status']}\n";
                break;
        }

        wp_send_json($redirects);

    }
    
    public static function get_url($url) {

        $http_request = array();
            
        $ch = curl_init($_POST['urls'][0]);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $http_request[0] = curl_exec($ch);

        $http_request['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        preg_match_all('/^Location:(.*)$/mi', $http_request[0], $matches);
        curl_close($ch);
        $http_request['redirect'] = !empty($matches[1]) ? trim($matches[1][0]) : 'No redirect found';
        return $http_request;
    }

}