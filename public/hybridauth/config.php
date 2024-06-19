<?php
include_once(dirname(__FILE__) .'/../../library/config.php');
global $facebook_api;
global $google_api;

$config = [
    //Location where to redirect users once they authenticate with a provider
    'callback' => '',

    //Providers specifics
    'providers' => [
        'Google'   => ['enabled' => true, 'keys' => [ 'id'  => "{$google_api['id']}", 'secret' => "{$google_api['secret']}"]], 
        'Facebook' => ['enabled' => true, 'keys' => [ "id" => "{$facebook_api['id']}", "secret" => "{$facebook_api['secret']}"] , "trustForwarded" => false, "scope"   => ['email'], "display" => "popup" ]
    ]
];