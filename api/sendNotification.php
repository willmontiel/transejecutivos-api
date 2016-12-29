<?php

require_once 'NotificationsManager.php';

$pushStatus = '0';

if(isset($_POST['submit'])) {
	$token = $_POST['token'];
    $message = $_POST['message'];
    if(isset($token) && isset($message)) {
        $notificationsManager = new NotificationsManager();
        $pushStatus = $notificationsManager->send(array($token), array("message" => $message, "msgtitle" => "Transportes Ejecutivos", "criteria" => 430531));
    }   
}
?>
<html>
    <head>
        <title>GCM Server</title>
    </head>
    <body style="text-align:center;color:blue">
    <h1>Google Cloud Messaging (GCM) Server</h1>
    <?php
        if('0' != $pushStatus) {
            $obj = json_decode($pushStatus);
            if($obj != null) {
                echo("<div style='color:green'>");
                echo("Success:".$obj->success);
                echo("<br/>Failure:".$obj->failure);
                echo("</div>");
            }
            else {
                echo("<div style='color:red'>".$pushStatus."</div>");
            }
        }
    ?>
    </body>
</html>