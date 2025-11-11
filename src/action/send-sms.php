<?php

/**
 * Send an SMS to all configured destinations
 */
function runAction($config)
{
    if (
        array_key_exists('action',       $config                      ) &&
        array_key_exists('send-sms',     $config['action']            ) &&
        array_key_exists('destinations', $config['action']['send-sms']) &&
        is_array($config['action']['send-sms']['destinations'])
    ) {
        require("../lib/FreemobileNotificationSender.php");

        // load SMS configuration
        $destinations = $config['action']['send-sms']['destinations'];
        $message = array_key_exists('msg', $_REQUEST) ? $_REQUEST['msg'] : "Notification HA";

        // send SMS for each configured destinations
        foreach ($destinations as $key => $dest) {
            try {
                $fms = new FreemobileNotificationSender($dest['sms-userid'], $dest['sms-apikey']);
                if (!isset($_REQUEST['silent'])) {
                    $fms->sendMessage("Message de test");
                    Logger::info("send-sms : SMS sent to [" . $dest['sms-userid'] . "] message = [" . $message . "]");
                } else {
                    Logger::info("send-sms : SILENT mode (no SMS sent) - sms-userid = [" . $dest['sms-userid'] . "] message = [" . $message . "]");
                }
            } catch (Exception $e) {
                Logger::error("send-sms : " . $errMsg, $_SERVER);
            }
        }
    } else {
        Logger::error("send-sms - invalid or missing configuration", $_SERVER);
        http_response_code(500);
    }
}
