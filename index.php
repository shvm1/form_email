<?php

        if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
                $email_to = !empty($_POST['email_to']) ? trim($_POST['email_to']) : false;
                $email_from = !empty($_POST['email_from']) ? trim($_POST['email_from']) : false;
                $name_from = !empty($_POST['name_from']) ? trim($_POST['name_from']) : '';
                $message = !empty($_POST['message']) ? $_POST['message'] : '';
                $subject = !empty($_POST['subject']) ? $_POST['subject'] : false;

                if(!$email_to || !preg_match('#.+@.+\..+#i',$email_to))
                        {
                                // no email_to
                                $error = 'Введите корректный email';
                                exit('<script>top.fail_request("'.$error.'");</script>');
                        }

                if(!$email_from || !preg_match('#.+@.+\..+#i',$email_from)) $email_from = 'noreply@'.$_SERVER['HTTP_HOST'];

                require_once 'classes/PHPMailer.php';
                $mail = new PHPMailer;
                $mail->setFrom($email_from, $name_from);
                $mail->addAddress($email_to);
                if($subject) $mail->Subject = $subject;
                $mail->msgHTML($message);
		
                if(!empty($_FILES['add_files']))
                        {
                        foreach($_FILES['add_files']['tmp_name'] as $k => $v)
                                {
                                if(!$v) continue;
                                if(is_uploaded_file($_FILES['add_files']['tmp_name'][$k]))
                                        {
                                        $mail->addAttachment($_FILES['add_files']['tmp_name'][$k], $_FILES['add_files']['name'][$k]);
                                        }
                                }
                        }
		
                if (!$mail->send()) 
                        {
                        $error =  $mail->ErrorInfo;
                        exit('<script>top.fail_request("'.$error.'");</script>');
                        } 
		
		
		
	exit('<script>top.success_request();</script>');
	}
       
        require 'view/form.php';
	
