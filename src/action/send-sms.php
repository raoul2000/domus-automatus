<?php

/**
 * Send an SMS to all configured destinations
 */
function runAction($config)
{
    
    if (
        array_key_exists('action', $config) && 
        array_key_exists('send-sms', $config['action']) && 
        array_key_exists('destinations', $config['action']['send-sms'])
    ) {
        require("../lib/FreemobileNotificationSender.php");

        // load SMS configuration
        $destinations = $config['action']['send-sms']['destinations'];
        $message = array_key_exists('msg', $_REQUEST) ? $_REQUEST['msg'] : "Notification envoyé";

        $date = date('m/d/Y h:i:s a', time());

        // send SMS for each configured destinations
        foreach ($destinations as $key => $dest) {
            try {
                $fms = new FreemobileNotificationSender($dest['sms-userid'], $dest['sms-apikey']);
                //$fms->sendMessage("Message de test");
                echo "[send-sms] ".$date." SMS OK to " . $dest['sms-userid'] . "<br/>";
                echo "message : <pre>".$message."</pre>";
            } catch (Exception $e) {
                $errMsg = "erreur for userid = " . $dest['sms-userid'] . " : " . $e->getMessage() . " code = " . $e->getCode();
                echo "SMS ERROR = $errMsg<br/>";
                file_put_contents("sms-error.log", $errMsg . "\n", FILE_APPEND | LOCK_EX );
            }
        }
    } else {
        echo "missing config";
        return 500;
    }
}
