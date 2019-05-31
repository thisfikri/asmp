<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_Handler extends CI_Model {

	public function send_email($to, $subject, $body, $html = FALSE)
	{
		$mail = new PHPMailer(true);
		try {
    		//Server settings
    		$mail->SMTPDebug = 0;                                 // Enable verbose debug output
    		$mail->isSMTP();                                      // Set mailer to use SMTP
    		$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    		$mail->SMTPAuth = true;                               // Enable SMTP authentication
    		$mail->Username = 'wisethinklab@gmail.com';                 // SMTP username
    		$mail->Password = 'hnobnfslvcaozmpk';                           // SMTP password
    		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    		$mail->Port = 587;
    		$mail->SMTPOptions = array(
    		'ssl' => array(
        			'verify_peer' => false,
        			'verify_peer_name' => false,
        			'allow_self_signed' => true
    			)
			);

			//Recipients
   			$mail->setFrom('postmaster@localhost', 'SIMAKWAPP');
    		$mail->addAddress($to);

    		//Contents
    		if ($html === TRUE) {
	    		$mail->isHTML(true);
	    	}

    		$mail->Subject = $subject;
    		$mail->Body = $body;
    		$mail->send();

    		return TRUE;

    	} catch(Exception $e) {

    		log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);

    		return FALSE;
    	}
	}
}