<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendMail($BodyMessage,$SubjectMessage)
{

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    $mail = new PHPMailer(true);                             
    try {
        $mail->SMTPDebug = 2;                                 
        $mail->isSMTP();                                    
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;     
        $mail->AuthType = 'NTLM';                       
        $mail->Username = 'systemautoemails@gmail.com';                
        $mail->Password = '';                         
        $mail->SMTPSecure = 'tls';                           
        $mail->Port = 587;                                 

        $mail->setFrom('systemautoemails@gmail.com');
        $mail->addAddress('danielandreson6@gmail.com');  
        
        $mail->isHTML(true);                                 
        $mail->Subject = $SubjectMessage;
        $mail->Body    = $BodyMessage;

    	$mail->SMTPOptions = array
        (
            'ssl' => array
            (
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->send();
     
    } catch (Exception $e) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>