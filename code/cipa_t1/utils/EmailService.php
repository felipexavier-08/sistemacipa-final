<?php

/**
 * Serviço de envio de emails
 * Funciona com mail() do PHP ou PHPMailer (se disponível)
 */
class EmailService {
    private $config;
    
    public function __construct() {
        $this->config = $this->getEmailConfig();
    }
    
    /**
     * Configurações de email
     */
    private function getEmailConfig() {
        // Configuração SendGrid (padrão)
        $config = [
            'host' => 'smtp.sendgrid.net',
            'port' => 587,
            'secure' => 'tls',
            'username' => 'apikey', // SendGrid usa 'apikey' como username
            'password' => 'SUA_API_KEY_AQUI', // SUBSTITUA COM SUA API KEY DO SENDGRID
            'from_email' => 'noreply@seusistema.com',
            'from_name' => 'Sistema CIPA'
        ];
        
        // Para ambiente de produção, pode ler de variáveis de ambiente
        if (getenv('EMAIL_HOST')) {
            $config = [
                'host' => getenv('EMAIL_HOST'),
                'port' => getenv('EMAIL_PORT') ?: 587,
                'secure' => getenv('EMAIL_SECURE') ?: 'tls',
                'username' => getenv('EMAIL_USERNAME'),
                'password' => getenv('EMAIL_PASSWORD'),
                'from_email' => getenv('EMAIL_FROM') ?: 'noreply@seusistema.com',
                'from_name' => getenv('EMAIL_FROM_NAME') ?: 'Sistema CIPA'
            ];
        }
        
        return $config;
    }
    
    /**
     * Envia email com código de voto
     */
    public function enviarCodigoVoto($email, $codigoVoto, $nomeFuncionario) {
        try {
            error_log("EMAIL SERVICE: Enviando código de voto para $email");
            
            $assunto = "Seu Código de Voto - Sistema CIPA";
            $mensagemHTML = $this->getTemplateEmail($codigoVoto, $nomeFuncionario);
            $mensagemTexto = $this->getTextEmail($codigoVoto, $nomeFuncionario);
            
            // Tentar com PHPMailer se disponível
            if ($this->usarPHPMailer()) {
                return $this->enviarComPHPMailer($email, $assunto, $mensagemHTML);
            }
            
            // Usar mail() do PHP
            return $this->enviarComMail($email, $assunto, $mensagemHTML, $mensagemTexto);
            
        } catch (Exception $e) {
            error_log("EMAIL SERVICE ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se deve usar PHPMailer
     */
    private function usarPHPMailer() {
        // Verificar se PHPMailer está disponível
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            return class_exists('PHPMailer\PHPMailer\PHPMailer');
        }
        
        return false;
    }
    
    /**
     * Envia usando PHPMailer
     */
    private function enviarComPHPMailer($email, $assunto, $mensagem) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuração SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['secure'];
            $mail->Port = $this->config['port'];
            
            // Remetente e destinatário
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($email);
            
            // Conteúdo
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;
            
            $enviado = $mail->send();
            
            if ($enviado) {
                error_log("EMAIL SUCCESS: PHPMailer enviou para $email");
            } else {
                error_log("EMAIL ERROR: PHPMailer falhou para $email");
            }
            
            return $enviado;
            
        } catch (Exception $e) {
            error_log("EMAIL ERROR: PHPMailer exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia usando mail() do PHP
     */
    private function enviarComMail($email, $assunto, $mensagemHTML, $mensagemTexto) {
        // Headers para email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">" . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Tentar HTML primeiro
        $enviado = mail($email, $assunto, $mensagemHTML, $headers);
        
        if (!$enviado) {
            // Fallback para texto puro
            error_log("EMAIL FALLBACK: Tentando texto puro para $email");
            $headersTexto = "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">" . "\r\n";
            $headersTexto .= "Reply-To: " . $email . "\r\n";
            $enviado = mail($email, $assunto, $mensagemTexto, $headersTexto);
        }
        
        error_log("EMAIL RESULT: " . ($enviado ? "SUCESSO" : "FALHA") . " para $email");
        return $enviado;
    }
    
    /**
     * Template HTML do email
     */
    private function getTemplateEmail($codigoVoto, $nomeFuncionario) {
        return "
            <html>
            <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; margin: 0;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                    <h2 style='color: #2c3e50; text-align: center; margin-bottom: 30px;'>🗳️ Código de Voto - Sistema CIPA</h2>
                    
                    <div style='background-color: #e8f5e8; padding: 20px; border-radius: 6px; margin: 20px 0; text-align: center;'>
                        <p style='margin: 0; font-size: 16px;'><strong>Seu Código de Voto:</strong></p>
                        <div style='background-color: #28a745; color: white; padding: 15px; border-radius: 4px; font-size: 24px; font-weight: bold; letter-spacing: 3px; margin: 15px 0;'>
                            " . strtoupper($codigoVoto) . "
                        </div>
                    </div>
                    
                    <div style='margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 6px;'>
                        <h3 style='color: #495057; margin-top: 0;'>📋 Instruções Importantes:</h3>
                        <ul style='color: #6c757d; line-height: 1.6;'>
                            <li>🔒 <strong>Guarde este código em local seguro</strong> - Ele é pessoal e intransferível</li>
                            <li>🗳️ <strong>Use este código para votar</strong> nas eleições CIPA</li>
                            <li>⚠️ <strong>Nunca compartilhe</strong> seu código com outras pessoas</li>
                            <li>🔐 <strong>Em caso de perda</strong>, procure o administrador do sistema</li>
                        </ul>
                    </div>
                    
                    <div style='margin-top: 30px; padding: 20px; background-color: #d1ecf1; border-radius: 6px; text-align: center;'>
                        <p style='margin: 0; color: #0c5460;'><strong>Olá, " . htmlspecialchars($nomeFuncionario) . "!</strong></p>
                        <p style='margin: 10px 0 0 0; color: #6c757d;'>Seja bem-vindo ao Sistema de Votação CIPA!</p>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 30px; padding: 20px; background-color: #2c3e50; color: white; border-radius: 0 0 8px 8px;'>
                    <p style='margin: 0; font-size: 12px;'>© 2026 - Sistema CIPA | Comissão Interna de Prevenção de Acidentes</p>
                </div>
            </body>
            </html>
        ";
    }
    
    /**
     * Template texto do email
     */
    private function getTextEmail($codigoVoto, $nomeFuncionario) {
        return "Código de Voto - Sistema CIPA\n\n" .
               "Olá, " . $nomeFuncionario . "!\n\n" .
               "Seu código de voto é: " . strtoupper($codigoVoto) . "\n\n" .
               "Instruções:\n" .
               "- Guarde este código em local seguro\n" .
               "- Use este código para votar nas eleições CIPA\n" .
               "- Nunca compartilhe seu código com outras pessoas\n" .
               "- Em caso de perda, procure o administrador do sistema\n\n" .
               "Sistema CIPA - Comissão Interna de Prevenção de Acidentes";
    }
    
    /**
     * Testa configuração de email
     */
    public function testarConfiguracao($testEmail) {
        try {
            $assunto = "Teste de Configuração - Sistema CIPA";
            $mensagem = "Este é um email de teste do Sistema CIPA.\n\nSe você recebeu este email, a configuração está funcionando corretamente.";
            
            if ($this->usarPHPMailer()) {
                return $this->enviarComPHPMailer($testEmail, $assunto, $mensagem);
            } else {
                $headers = "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">" . "\r\n";
                return mail($testEmail, $assunto, $mensagem, $headers);
            }
            
        } catch (Exception $e) {
            error_log("EMAIL TEST ERROR: " . $e->getMessage());
            return false;
        }
    }
}

?>
