<?php
namespace Discursivamente\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    public static function sendVerificationCode($email, $code) {
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USERNAME') ?: 'projeto.discursivamente@gmail.com';
            $mail->Password   = getenv('SMTP_PASSWORD') ?: 'ksxjnadjxrlnzqta';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT') ?: 587;
            
            // Definição dos cabeçalhos, remetente e destinatário
            $mail->setFrom('projeto.discursivamente@gmail.com', 'DISCURSIVAMENTE');
            $mail->addAddress($email);
            $mail->addReplyTo('suporte@seudominio.com', 'Suporte Discursivamente');
            $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
            
            // Configuração DKIM (ajuste conforme seu domínio e caminho para a chave privada)
            $mail->DKIM_domain = 'seudominio.com';
            $mail->DKIM_private = '/caminho/para/sua_chave_privada.key';
            $mail->DKIM_selector = 'default';
            $mail->DKIM_passphrase = '';
            $mail->DKIM_identity = $mail->From;
            
            // Conteúdo do email com template HTML e fallback para texto simples
            $mail->isHTML(true);
            $mail->Subject = 'Código de Verificação - DISCURSIVAMENTE';
            $mail->Body = '
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Código de Verificação - DISCURSIVAMENTE</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { background-color: #ffffff; margin: 20px auto; padding: 20px; border-radius: 5px; max-width: 600px; }
                    .header { text-align: center; padding-bottom: 10px; }
                    .code { font-size: 24px; color: #007BFF; font-weight: bold; }
                    .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Bem-vindo ao DISCURSIVAMENTE</h2>
                    </div>
                    <p>Para concluir seu cadastro, utilize o código de verificação abaixo:</p>
                    <p class="code">' . $code . '</p>
                    <p>Se você não solicitou este código, por favor, ignore este email.</p>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' DISCURSIVAMENTE. Todos os direitos reservados.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            // Versão alternativa para clientes que não suportam HTML
            $mail->AltBody = "Bem-vindo ao DISCURSIVAMENTE.\n\nPara concluir seu cadastro, utilize o código de verificação: " . $code . "\n\nSe você não solicitou este código, por favor, ignore este email.\n\nDISCURSIVAMENTE";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar email: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
