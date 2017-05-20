<?php
$errorMessageEmpty = json_encode(array('message' => 'No arguments provided'));
// Check for empty fields
if(empty($_POST['name']) ||
    empty($_POST['email']) ||
    empty($_POST['subject']) ||
    empty($_POST['message']) ||
    !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
{
    echo $errorMessageEmpty;
    return false;
}
require_once('email_config.php');
require('../PHPMailer/PHPMailerAutoload.php');
$mail = new PHPMailer;
$mail->SMTPDebug = 0; // Enable verbose debug output

$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication


$mail->Username = EMAIL_USER; // SMTP username
$mail->Password = EMAIL_PASS; // SMTP password
$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587; // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = $_POST['email']; // who sent it. gmail will rewrite who sent it to show "me" as the sender, so I add in the replyTo address so I know who sent it
$mail->FromName = strip_tags(htmlspecialchars($_POST['name']));//your email sending account name
$mail->addAddress(EMAIL_USER, 'andgasperdev.com'); // Send the mail to account
//$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo($_POST['email']); // Add the sender's email address in the reply to setting
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

// Pull mail information from the POST super global using the names of the input fields
$mail->Subject = strip_tags(htmlspecialchars($_POST['subject']));
$mail->Body    = strip_tags(htmlspecialchars($_POST['message']));
$mail->AltBody = strip_tags(htmlspecialchars($_POST['message']));

$errorMessage = json_encode(array('error' => 'Message could not be sent.'));
$successMessage = json_encode(array("success" => "Message Sent")); // Encode the success message as JSON.
if(!$mail->send()) {
    echo $errorMessage;
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo $successMessage ;
}
?>
