<?php

$config = [
    "authorizationKey" => 'abcd',
    "param1" => true,
    "timezone" => 'Europe/Paris',

    "action" => [
        
        ///////////////////////////////////////////////////////////////////////////////
        // SMS Service Configuration

        "send-sms" => [
            'destinations' => [
                ["sms-userid" => 'AXXXXX',  'sms-apikey' => 'YYYYYYYY'],
                ["sms-userid" => 'BXXXXX',  'sms-apikey' => 'YYYYYYYY']
            ]
        ]
    ]
];
