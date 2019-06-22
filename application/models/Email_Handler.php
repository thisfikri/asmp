<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ASMP - Aplikasi Sistem Menejemen Perkantoran
 *
 * @package ASMP
 * @author ThisFikri (Leader)
 * @copyright Copyright (c) 2018, Recodech <ocraineore@gmail.com>
 * @link https://github.com/codecoretech
 * @version BETA BUILD 02
 * @since Aplha 1.0.0
 * @license GNU GPL v3.0
 *
 * Aplikasi ini dibuat dan dikembangkan untuk dipergunakan dalam hal administrasi perkantoran
 */


 /**
  * Email_Handler Class
  * 
  * Menghandle email
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
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
    		$mail->Username = 'corecodetech@gmail.com';                 // SMTP username (Must Change To codecoretech email)
    		$mail->Password = 'makdxbxogynooojf';                           // SMTP password
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
   			$mail->setFrom('noreply@localhost', 'ASMP');
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