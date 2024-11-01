<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

register_setting(
    'skrumpt_form_option_group', // option_group
    'slf_settings', // option_name
    array( $this, 'skrumpt_form_sanitize' ) // sanitize_callback
);

add_settings_section(
    'skrumpt_form_setting_section', // id
    'Settings', // title
    array( $this, 'skrumpt_form_section_info' ), // callback
    'skrumpt-form-admin' // page
);

$skrumpt_form_options = get_option( 'slf_settings' );
$api_key = $skrumpt_form_options['api_key'];

if($api_key != "" || $api_key != null){

    $wpform_url_details = "https://api.skrumpt.com/v1/tokenize/details/wpform";


    $response = wp_remote_get( $wpform_url_details , 
        [
            'sslverify' => false,
            'timeout' => 10,
            'headers' => [
                'Authorization' =>  'Bearer ' . $api_key
            ]
        ]
    );

    if ( is_wp_error( $response ) ) {
        
        return false;
    }


    
    $response_arr = json_decode($response['body'], true);

    if($response_arr['success']){

        update_option( 'skrumpt_api_key_status', 'connected', 'yes' );

        $account_details = '
            <div style="margin-bottom:10px">Account CRM Status : <strong style="color:#15952f">Connected</strong></div>
            <div>Account ID : <strong>' . $response_arr['details']['account']['accountID'] . '</strong></div>
            <div>Account Name: <strong>' . $response_arr['details']['account']['firstname'] . ' ' . $response_arr['details']['account']['lastname'] . '</strong></div>
            <div style="margin-bottom:10px">Account Email : <strong>' . $response_arr['details']['account']['email'] . '</strong></div>    
        ';

        add_settings_field(
            'api_key', // id
            'API Key', // title
            array( $this, 'api_key_callback' ), // callback
            'skrumpt-form-admin', // page
            'skrumpt_form_setting_section',
            [
                'details' => $account_details
            ]
        );    
    
        add_settings_field(
            'campaign', // id
            'Campaign', // title
            array( $this, 'campaign_callback' ), // callback
            'skrumpt-form-admin', // page
            'skrumpt_form_setting_section', // section
            $response_arr['details']['campaigns']
        );
    
        add_settings_field(
            'success_message', // id
            'Success Message', // title
            array( $this, 'success_message_callback' ), // callback
            'skrumpt-form-admin', // page
            'skrumpt_form_setting_section' // section
        );

        add_settings_field(
            'success_redirect_url', // id
            'Success Redirect URL', // title
            array( $this, 'success_redirect_url_callback' ), // callback
            'skrumpt-form-admin', // page
            'skrumpt_form_setting_section' // section
        );


    }else{

        update_option( 'skrumpt_api_key_status', 'invalid', 'yes' );

        $account_details = '
            <div style="color:red">You entered an invalid API key. Please ensure that your API key is correct.</div>  
        ';

        add_settings_field(
            'api_key', // id
            'API Key', // title
            array( $this, 'api_key_callback' ), // callback
            'skrumpt-form-admin', // page
            'skrumpt_form_setting_section',
            [
                'details' => $account_details
            ]
        );    
    }

}else{
    $account_details = '
        <div>Before we get started we need to get your Skrumpt API Key.  API key needs to be requested at https://crm.skrumpt.com/</div>  
    ';

    update_option( 'skrumpt_api_key_status', 'empty', 'yes' );
    
    add_settings_field(
        'api_key', // id
        'API Key', // title
        array( $this, 'api_key_callback' ), // callback
        'skrumpt-form-admin', // page
        'skrumpt_form_setting_section',
        [
            'details' => $account_details
        ]
    );
}