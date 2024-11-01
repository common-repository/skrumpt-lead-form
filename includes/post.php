<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

try {

    $skrumpt_form_options = get_option( 'slf_settings' ); // Array of All Options
    $api_key = $skrumpt_form_options['api_key'];
    $campaign_id = $skrumpt_form_options['campaign'];
    
    $url = "https://api.skrumpt.com/v1/tokenize/leads/wpform";
    
    $valid_titles = [
        "Mr",
        "Mrs",
        "Miss",
        "Ms",
        "Dr",
        "Other"
    ];

    $valid_property_types = [
        "Self-Contained Studio",
        "1-Bed Flat",
        "2-Bed Flat",
        "3-Bed Flat",
        "4-Bed Flat",
        "1-Bed Terraced",
        "2-Bed Terraced",
        "3-Bed Terraced",
        "4-Bed Terraced",
        "5-Bed Terraced",
        "6-Bed Terraced",
        "1-Bed Semi-Detached",
        "2-Bed Semi-Detached",
        "3-Bed Semi-Detached",
        "4-Bed Semi-Detached",
        "5-Bed Semi-Detached",
        "6-Bed Semi-Detached",
        "1-Bed Detached",
        "2-Bed Detached",
        "3-Bed Detached",
        "4-Bed Detached",
        "5-Bed Detached",
        "6-Bed Detached",
        "1-Bed Apartment",
        "2-Bed Apartment",
        "3-Bed Apartment",
        "4-Bed Apartment",
        "5-Bed Apartment",
        "Land",
        "Guest House",
        "Hotel"
    ];

    $valid_reason_for_selling = [
        "Broken Chain",
        "Reposession",
        "Debt Problems",
        "Need Cash",
        "Deceased Estate",
        "Divorce",
        "Family Member Died",
        "Ill-Health",
        "No Buyers",
        "Equity Release",
        "Relocating",
        "Retiring",
        "Money For Children",
        "Distressed Property",
        "Unprofitable Investment",
        "Haunted House",
        "Down Sizing",
        "Alternative to Refinancing",
        "Wrong Number / No Email",
        "Downsizing but not in any hurry",
        "Other"
    ];

    if(!isset($_POST['title']) || empty($_POST['title']) ){
        throw new Exception("Please select a title");
    }else{
        if( !in_array($_POST['title'], $valid_titles) ){
            throw new Exception("Invalid title value");
        }
    }

    if(!isset($_POST['firstname']) || empty($_POST['firstname']) ){
        throw new Exception("Please enter a firstname");
    }
    if(!isset($_POST['lastname']) || empty($_POST['lastname']) ){
        throw new Exception("Please enter a lastname");
    }
    if(!isset($_POST['telephone']) || empty($_POST['telephone']) ){
        throw new Exception("Please enter a telephone");
    }
    if(!isset($_POST['mobile']) || empty($_POST['mobile']) ){
        throw new Exception("Please enter a mobile");
    }
    if(!isset($_POST['email']) || empty($_POST['email']) ){
        throw new Exception("Please enter an email");
    }

    if(!is_email($_POST['email'])){
        throw new Exception("The email you entered is invalid");
    }
        
    if(!isset($_POST['address']) || empty($_POST['address']) ){
        throw new Exception("Please enter an address");
    }
    if(!isset($_POST['city']) || empty($_POST['city']) ){
        throw new Exception("Please enter a city");
    }
    if(!isset($_POST['county']) || empty($_POST['county']) ){
        throw new Exception("Please enter a county");
    }
    if(!isset($_POST['country']) || empty($_POST['country']) ){
        throw new Exception("Please enter a country");
    }
    if(!isset($_POST['postcode']) || empty($_POST['postcode']) ){
        throw new Exception("Please enter a postcode");
    }
    if(!isset($_POST['type']) || empty($_POST['type']) ){
        throw new Exception("Please select a property type");
    }else{
        if( !in_array($_POST['type'], $valid_property_types) ){
            throw new Exception("Invalid property type value");
        }
    }

    if( isset($_POST['reasonForSelling']) && $_POST['reasonForSelling'] != "" ){
        if( !in_array($_POST['reasonForSelling'], $valid_reason_for_selling) ){
            throw new Exception("Invalid reason for selling value");
        }
    }

    $data = [
        "title" => sanitize_text_field($_POST['title']),
        "firstname" => sanitize_text_field($_POST['firstname']),
        "lastname" => sanitize_text_field($_POST['lastname']),
        "telephone" => sanitize_text_field($_POST['telephone']),
        "mobile" => sanitize_text_field($_POST['mobile']),
        "email" => sanitize_email($_POST['email']),
        "address" => sanitize_text_field($_POST['address']),
        "city" => sanitize_text_field($_POST['city']),
        "county" => sanitize_text_field($_POST['county']),
        "country" => sanitize_text_field($_POST['country']),
        "postcode" => sanitize_text_field($_POST['postcode']),
        "type" => sanitize_text_field($_POST['type']),
        "estimatedValue" => sanitize_text_field($_POST['estimatedValue']),
        "estimatedSecuredDebts" => sanitize_text_field($_POST['estimatedSecuredDebts']),
        "reasonForSelling" => sanitize_text_field($_POST['reasonForSelling']),
        "campaignID" => sanitize_text_field($_POST['campaignID']),
    ];
    
    $nonce = sanitize_text_field($_POST['nonce']);
    
    if ( !wp_verify_nonce( $nonce, 'postproperty' ) ) {
    
        throw new Exception("Invalid Request");
    }
    
    if(isset($_POST['campaign_id']) && $_POST['campaign_id'] != ""){
        $data['campaignID'] = sanitize_text_field($_POST['campaign_id']);
    }else{
        if($campaign_id != "" && $campaign_id != null){
            $data['campaignID'] = $campaign_id;
        }
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
        echo $response['body'];
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