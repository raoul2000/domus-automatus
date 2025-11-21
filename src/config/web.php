<?php

$config = [
    "authorizationKey" => 'abcd',
    "param1" => true,
    "timezone" => 'Europe/Paris',
    "logDir" => __DIR__ . '/../log',  // will be created if needed

    "ftp" => [
        "host" => 'host_or_ip',
        "username" => "bob",
        "password" => "secretpass",
        "rootDir" => "domoticus"
    ],
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
