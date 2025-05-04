<?php

namespace Infrastructure\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ResetPasswordMailer
{
    public static function send($to, $name, $code)
    {
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = trim($_ENV['MAIL_PASSWORD'], '"');
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de senha - Discursivamente';
            $mail->Body = "Olá, $name!<br><br>
                Seu código de recuperação de senha é: <b>$code</b><br>
                Ele é válido por 15 minutos.<br><br>
                Se não foi você, ignore este e-mail.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail de recuperação: ' . $mail->ErrorInfo);
            return false;
        }
    }
}