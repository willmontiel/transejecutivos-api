<html>
<head>
<title>PHPMailer - SMTP advanced test with authentication</title>
</head>
<body>

<?php

require_once('../class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  $mail->Host       = "smtp.mandrillapp.com"; // SMTP server
  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->Host       = "smtp.mandrillapp.com"; // sets the SMTP server
  $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
  $mail->Username   = "ventas@lospinosfincaraiz.com"; // SMTP account username
  $mail->Password   = "5fda8ad6-a634-453a-987a-4783765e434f";        // SMTP account password
  $mail->AddAddress('ventas@zonaenlinea.com', 'Ventas Diego Gomez ');
  $mail->SetFrom('info@zonaenlinea.com', 'Diego Gomez info');
  $mail->AddReplyTo('info@zonaenlinea.com', 'Diego Gomez info');
  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML(file_get_contents('contents.html'));
  $mail->AddAttachment('images/phpmailer.gif');      // attachment
  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
  $mail->Send();
  echo "Message Sent OK</p>\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
