<?php

namespace Infrastructure\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CourseSubmissionMailer
{
    public static function send($data, $file)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = trim($_ENV['MAIL_PASSWORD'], '"');
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($_ENV['MAIL_FROM_ADDRESS']); // Vai para você mesmo

            // Anexo do curso
            $mail->addAttachment($file['tmp_name'], $file['name']);

            // Corpo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Novo Curso Enviado: ' . $data['titulo'];
            $mail->Body = "
                <h3>Detalhes do curso enviado</h3>
                <p><strong>Nome:</strong> {$data['nome']}</p>
                <p><strong>E-mail:</strong> {$data['email']}</p>
                <p><strong>Título:</strong> {$data['titulo']}</p>
                <p><strong>Descrição:</strong><br>{$data['descricao']}</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar curso: {$mail->ErrorInfo}");
            return false;
        }
    }
}
