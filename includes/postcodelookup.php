<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

try {

    $skrumpt_form_options = get_option( 'slf_settings' ); // Array of All Options
    $api_key = $skrumpt_form_options['api_key'];
    $campaign_id = $skrumpt_form_options['campaign'];
    
    $url = "http://api.skrumpt.com/v1/tokenize/postcode-lookup";

    if(!isset($_POST['postcode']) || empty($_POST['postcode']) ){
        throw new Exception("Please enter a postcode");
    }

    $data = [
        "postcode" => sanitize_text_field($_POST['postcode']),
    ];
    
    $nonce = sanitize_text_field($_POST['nonce']);
    
    if ( !wp_verify_nonce( $nonce, 'postcodelookup' ) ) {
    
        throw new Exception("Invalid Request");
    }
    
    $headers = [
        "Content-Type" => 'application/x-www-form-urlencoded',
        "Authorization" => 'Bearer ' . $api_key
    ];
    
    $response = wp_remote_post( $url, array(
        'sslverify'   => false,
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
        'body'        => $data,
        'cookies'     => array()
        )
    );
    
    if ( is_wp_error( $response ) ) {

        $error_message = $response -> get_error_message();
        throw new Exception("Something went wrong: " . $error_message);
        
    } else {

        header('Content-Type: application/json');
        $response_body = $response['body'];

        echo $response_body;
    }
    
    wp_die(); 


}catch(Exception $e) {
    header('Content-Type: application/json');

    $response = [
        "success" => false,
        "message" => $e -> getMessage(),
    ];

    $json_response = json_encode($response);
    echo $json_response;
    wp_die(); 
}