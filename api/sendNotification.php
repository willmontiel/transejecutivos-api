<?php
define('GOOGLE_API_KEY', 'AIzaSyDGbUbtt-3-lefRTPKGA16VsUZvX9xq1Ec');//Replace with your Key

$pushStatus = '0';;

if(isset($_POST['submit'])) {
	$gcmRegIds = array("eaBPxOwjWhI:APA91bFQuyN_k8S8QDods6BFnM6WJADxh9CgjwC9E8BPtq8i4AbjMRvOe9salwEu7TPUpZSzQqK0EgOynw947CxTQqsLiQI3XN7DOoIZ_zSk4G2jCpzR_MVSnLt905QnBf2pUNUJPSUX");
    $pushMessage = $_POST['message'];
    if(isset($gcmRegIds) && isset($pushMessage)) {
        $message = array('message' => $pushMessage);
        $pushStatus = sendPushNotification($gcmRegIds, $message);
    }   
}

function sendPushNotification($registration_ids, $message) {
            // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
            'registration_ids' => $registration_ids,
            'data' => array("message" => $message, "msgtitle" => "Transportes Ejecutivos", "idservice" => "1"),
            //'data' => $message,
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
?>
<html>
    <head>
        <title>GCM Server</title>
    </head>
    <body style="text-align:center;color:blue">
    <h1>Google Cloud Messaging (GCM) Server</h1>
    <form method = 'POST' action = ''>
        <div>
            <textarea rows = 6 name = "message" cols = 50 placeholder = 'Messages send to all device in database via GCM'></textarea>
        </div>
        <div style="margin-top:10px">
            <input type = 'submit' name="submit" value = 'Send Notification'>
        </div>
        <p>
			<h3>
			<?php
				if('0' != $pushStatus)
				{
					$obj = json_decode($pushStatus);
					if($obj != null)
					{
						echo("<div style='color:green'>");
						echo("Success:".$obj->success);
						echo("<br/>Failure:".$obj->failure);
						echo("</div>");
					}
					else
					{
						echo("<div style='color:red'>".$pushStatus."</div>");
					}
				}
			?>
			</h3>
		</p>
    </form>
    </body>
</html>
