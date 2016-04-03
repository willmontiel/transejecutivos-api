<html>
<head>
<title>PHPMailer - SMTP (Gmail) advanced test</title>
</head>
<body>

<?php
$texto = "Texto en php";
require_once('../class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  $mail->Host       = "smtp.gmail.com"; // SMTP server
  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
  $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
  $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
  $mail->Username   = "info@zonaenlinea.com";  // GMAIL username
  $mail->Password   = "Diegomez1976";            // GMAIL password
  $mail->AddAddress('ventas@zonaenlinea.com', 'John Doe');
  $mail->SetFrom('info@zonaenlinea.com', 'First Last');
  $mail->AddReplyTo('info@zonaenlinea.com', 'First Last');
  $mail->Subject = '-//-PHPMailer Test Subject via mail(), advanced';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML('<body style="margin: 10px;">
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
<div align="center"><img src="images/phpmailer.gif" style="height: 90px; width: 340px"></div>
<p><br>
  <br>
  &nbsp;This is a test of PHPMailer.</p>
'.$texto.' &nbsp;</p>
<p>&nbsp;</p>
<p><br>
  <br>
  This particular example uses <strong>HTML</strong>, with a &lt;div&gt; tag and inline<br>
  styles.<br>
  <br>
  Also note the use of the PHPMailer logo above with no specific code to handle
  including it.<br />
  Included are two attachments:<br />
  phpmailer.gif is an attachment and used inline as a graphic (above)<br />
  phpmailer_mini.gif is an attachment<br />
  <br />
  PHPMailer:<br />
  Author: Andy Prevost (codeworxtech@users.sourceforge.net)<br />
  Author: Marcus Bointon (coolbru@users.sourceforge.net)<br />
</p>
</div>
</body>');
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
