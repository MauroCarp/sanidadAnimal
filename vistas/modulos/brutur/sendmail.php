<?php

function sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject, $template){

	require  $_SERVER['DOCUMENT_ROOT'].'/sanidadAnimal/extensiones/PHPMailer/PHPMailerAutoload.php';
	
	$mail = new PHPMailer;
	
	$mail->isSMTP();                            // Establecer el correo electrónico para utilizar SMTP
	
	$mail->Host = 'smtp.gmail.com';             // Especificar el servidor de correo a utilizar 
	
	$mail->SMTPAuth = true;                     // Habilitar la autenticacion con SMTP
	
	$mail->Username = $mail_username;          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	
	$mail->Password = $mail_userpassword; 		// Tu contraseña de gmail
	
	$mail->SMTPSecure = 'tls';                  // Habilitar encriptacion, `ssl` es aceptada
	
	$mail->Port = 587;                          // Puerto TCP  para conectarse 
	
	$mail->setFrom($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
	
	$mail->addReplyTo($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
	
	$mail->addAddress($mail_addAddress);   // Agregar quien recibe el e-mail enviado
	
	$message = file_get_contents($template);
	
	$message = str_replace('{{first_name}}', $mail_setFromName, $message);
	
	$message = str_replace('{{message}}', $txt_message, $message);
	
	$message = str_replace('{{customer_email}}', $mail_setFromEmail, $message);
	
	$mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
	
	$mail->Subject = $mail_subject;
	
	$mail->msgHTML($message);
	
	$mailEnviado = TRUE;
	
	$enviado = true;

	if(!$mail->send()) {

		echo "<h3>Hubo un error al enviar<h3>";
		
	}else{
		
		echo "<h3>Notificacion enviada correctamente<h3>";
	
	}
		
}

       
function sendemailAttach($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject, $template,$rutaAttach){
                
	require $_SERVER['DOCUMENT_ROOT'].'/sanidadAnimal/extensiones/PHPMailer/PHPMailerAutoload.php';
	$mail = new PHPMailer;
	$mail->isSMTP();                            // Establecer el correo electrónico para utilizar SMTP
	$mail->Host = 'smtp.gmail.com';             // Especificar el servidor de correo a utilizar 
	$mail->SMTPAuth = true;                     // Habilitar la autenticacion con SMTP
	$mail->Username = $mail_username;          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail->Password = $mail_userpassword; 		// Tu contraseña de gmail
	$mail->SMTPSecure = 'tls';                  // Habilitar encriptacion, `ssl` es aceptada
	$mail->Port = 587;                          // Puerto TCP  para conectarse 
	$mail->setFrom($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
	$mail->addReplyTo($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
	$mail->addAddress($mail_addAddress);   // Agregar quien recibe el e-mail enviado
	$message = file_get_contents($template);
	$message = str_replace('{{first_name}}', $mail_setFromName, $message);
	$message = str_replace('{{message}}', $txt_message, $message);
	$message = str_replace('{{customer_email}}', $mail_setFromEmail, $message);
	$mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
	
	$mail->Subject = $mail_subject;
	$archivo = $rutaAttach;
	$mail->AddAttachment($archivo,$archivo);
	$mail->msgHTML($message);
	if(!$mail->send()) {
		return $mail->ErrorInfo;
	} else {
		return 'ok';
	}
	
}

?>