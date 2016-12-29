<?php

define('GOOGLE_API_KEY', 'AIzaSyDGbUbtt-3-lefRTPKGA16VsUZvX9xq1Ec');//Replace with your Key

class NotificationsManager {

    function send($tokens, $data) {
            // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
            'registration_ids' => $tokens,
            //'data' => array("message" => $message, "msgtitle" => "Transportes Ejecutivos", "criteria" => "1"),
            'data' => $data,
        );
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
		return $result;
    }
}