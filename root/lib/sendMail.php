<?php
	function sendMail(
		$email,
		$subject,
		$content,
		$contentNonHTML = ''
	) {
		require_once(ROOT . '/lib/phpmailer/class.phpmailer.php');

		$mail = new PHPMailer;

		$mail->IsMail(); // Set mailer to use SMTP
		$mail->Host       = 'localhost'; // Specify main and backup server
		$mail->SMTPAuth   = true; // Enable SMTP authentication
		$mail->Username   = 'bugs@code-sharks.pl'; // SMTP username
		$mail->Password   = 'bugsbugsbugs'; // SMTP password
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPDebug  = 1; // Enable encryption, 'ssl' also accepted
		$mail->CharSet    = "UTF-8";

		$mail->From     = 'bugs@code-sharks.pl';
		$mail->FromName = 'CodeSharks Team';
		$mail->AddAddress($email);
//$mail->AddReplyTo('info@example.com', 'Information');

		$mail->WordWrap = 50; // Set word wrap to 50 characters
//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->IsHTML(true); // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $content;
		if ($content != '') {
			$mail->AltBody = $contentNonHTML;
		}

		if (!$mail->Send()) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;

			return false;
		} else {
			return true;
		}
	}