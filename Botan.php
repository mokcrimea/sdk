<?php

namespace YourProject;

/**
 * Class Botan
 * @package YourProject
 *
 * Usage:
 *
 * private $token = 'token';
 *
 * public function _incomingMessage($message_json) {
 *     $messageObj = json_decode($message_json, true);
 *     $messageData = $messageObj['message'];
 *
 *     $botan = new Botan($this->token);
 *     $botan->track($messageData, 'Start');
 * }
 *
 */

class Botan {

    private $template_uri = 'https://api.botan.io/track?token=#TOKEN&uid=#UID&name=#NAME';
    private $token;

    function __construct($token) {
        $this->token = $token;
    }

    private function request($url, $body) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($body)
        ));
        $response = curl_exec($ch);
        $error = false;
        if (!$response){
            $error = true;
        }
        $responseData = json_decode($response, true);
        curl_close($ch);

        return array(
            'error' => $error,
            'response' => $responseData
        );
    }

    public function track($message, $event_name) {
        $uid = $message['from']['id'];
        $event_name = $event_name ? $event_name : 'Message';
        $url = str_replace(
            array('#TOKEN', '#UID', '#NAME'), 
            array($this->token, $uid, $event_name), 
            $this->template_uri
        );
        $result = $this->request($url, $message);
    }
}