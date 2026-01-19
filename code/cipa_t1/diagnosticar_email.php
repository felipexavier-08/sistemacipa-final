<?php
/**
 * Script de diagnóstico completo para problemas de email
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Email - Sistema CIPA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        h1, h2 { color: #2c3e50; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Diagnóstico Completo de Email</h1>
        
        <?php
        // 1. Verificar configuração básica
        echo "<div class='step'>";
        echo "<h2>1️⃣ Verificação Básica</h2>";
        
        if (function_exists('mail')) {
            echo "<div class='success'>✅ Função mail() está disponível</div>";
        } else {
            echo "<div class='error'>❌ Função mail() NÃO está disponível</div>";
        }
        
        echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
        echo "<p><strong>Sistema Operacional:</strong> " . php_uname() . "</p>";
        echo "</div>";
        
        // 2. Verificar configuração do php.ini
        echo "<div class='step'>";
        echo "<h2>2️⃣ Configuração do php.ini</h2>";
        
        $sendmail_path = ini_get('sendmail_path');
        $smtp_host = ini_get('SMTP');
        $smtp_port = ini_get('smtp_port');
        
        echo "<p><strong>sendmail_path:</strong> " . ($sendmail_path ?: 'não definido') . "</p>";
        echo "<p><strong>SMTP:</strong> " . ($smtp_host ?: 'não definido') . "</p>";
        echo "<p><strong>smtp_port:</strong> " . ($smtp_port ?: 'não definido') . "</p>";
        
        if (empty($sendmail_path) && empty($smtp_host)) {
            echo "<div class='warning'>⚠️ Nenhuma configuração de email encontrada no php.ini</div>";
        }
        echo "</div>";
        
        // 3. Testar configuração do sendmail
        echo "<div class='step'>";
        echo "<h2>3️⃣ Verificar Sendmail/Postfix</h2>";
        
        if (file_exists('/usr/sbin/sendmail')) {
            echo "<div class='success'>✅ Sendmail encontrado em /usr/sbin/sendmail</div>";
            
            // Verificar se está rodando
            $sendmail_status = shell_exec('systemctl is-active sendmail 2>/dev/null || echo "não instalado/não rodando"');
            echo "<p><strong>Status Sendmail:</strong> " . trim($sendmail_status) . "</p>";
        } else {
            echo "<div class='warning'>⚠️ Sendmail não encontrado</div>";
        }
        
        if (file_exists('/usr/sbin/postfix')) {
            echo "<div class='success'>✅ Postfix encontrado</div>";
            
            $postfix_status = shell_exec('systemctl is-active postfix 2>/dev/null || echo "não instalado/não rodando"');
            echo "<p><strong>Status Postfix:</strong> " . trim($postfix_status) . "</p>";
        } else {
            echo "<div class='warning'>⚠️ Postfix não encontrado</div>";
        }
        echo "</div>";
        
        // 4. Teste simples de email
        if (isset($_POST['test_simple'])) {
            echo "<div class='step'>";
            echo "<h2>4️⃣ Teste Simples de Email</h2>";
            
            $test_email = $_POST['test_email'];
            $subject = "Teste Simples - Sistema CIPA";
            $message = "Este é um teste simples do Sistema CIPA.\n\nData: " . date('d/m/Y H:i:s');
            $headers = "From: Sistema CIPA <cipa@localhost>\r\n";
            
            echo "<p><strong>Enviando para:</strong> $test_email</p>";
            echo "<p><strong>Assunto:</strong> $subject</p>";
            echo "<p><strong>Mensagem:</strong></p>";
            echo "<pre>" . htmlspecialchars($message) . "</pre>";
            echo "<p><strong>Headers:</strong></p>";
            echo "<pre>" . htmlspecialchars($headers) . "</pre>";
            
            $result = mail($test_email, $subject, $message, $headers);
            
            if ($result) {
                echo "<div class='success'>✅ Email enviado com sucesso!</div>";
                echo "<p>Verifique sua caixa de entrada (e também spam/lixeira)</p>";
            } else {
                echo "<div class='error'>❌ Falha ao enviar email</div>";
                echo "<p>Verifique os logs do sistema para mais detalhes</p>";
            }
            echo "</div>";
        }
        
        // 5. Teste com EmailService
        if (isset($_POST['test_service'])) {
            echo "<div class='step'>";
            echo "<h2>5️⃣ Teste com EmailService</h2>";
            
            require_once __DIR__ . '/utils/EmailService.php';
            
            $test_email = $_POST['test_email'];
            $emailService = new EmailService();
            
            echo "<p><strong>Enviando para:</strong> $test_email</p>";
            
            $result = $emailService->testarConfiguracao($test_email);
            
            if ($result) {
                echo "<div class='success'>✅ EmailService enviou com sucesso!</div>";
            } else {
                echo "<div class='error'>❌ EmailService falhou</div>";
            }
            echo "</div>";
        }
        ?>
        
        <div class="step">
            <h2>🧪 Testes Disponíveis</h2>
            
            <form method="post">
                <h3>Teste 1: Email Simples (mail())</h3>
                <label>Email: 
                    <input type="email" name="test_email" placeholder="seuemail@teste.com" required style="width: 300px; padding: 5px;">
                </label>
                <button type="submit" name="test_simple" class="btn">📧 Testar mail()</button>
            </form>
            
            <form method="post">
                <h3>Teste 2: EmailService</h3>
                <label>Email: 
                    <input type="email" name="test_email" placeholder="seuemail@teste.com" required style="width: 300px; padding: 5px;">
                </label>
                <button type="submit" name="test_service" class="btn btn-success">📧 Testar EmailService</button>
            </form>
        </div>
        
        <div class="step">
            <h2>🛠️ Soluções Recomendadas</h2>
            
            <h3>Opção 1: Instalar Postfix (Recomendado para Linux)</h3>
            <pre>sudo apt-get update
sudo apt-get install postfix
# Configurar como "Internet Site"
# Usar hostname padrão</pre>
            
            <h3>Opção 2: Configurar SendGrid SMTP (Recomendado)</h3>
            <p>Use o script <a href="/code/cipa_t1/configurar_sendgrid.php">configurar_sendgrid.php</a> para configurar SendGrid</p>
            <p><strong>100 emails/dia grátis - Ideal para produção</strong></p>
            
            <h3>Opção 3: Configurar Gmail SMTP</h3>
            <p>Use o script <a href="/code/cipa_t1/configurar_email.php">configurar_email.php</a> para configurar Gmail SMTP</p>
            
            <h3>Opção 4: Usar serviço externo</h3>
            <p>Outros serviços como Mailgun, Amazon SES, etc.</p>
        </div>
        
        <div class="step">
            <h2>📝 Comandos Úteis</h2>
            <pre># Verificar logs de email
tail -f /var/log/mail.log

# Verificar status do postfix
sudo systemctl status postfix

# Reiniciar postfix
sudo systemctl restart postfix

# Testar sendmail manualmente
echo "Teste" | /usr/sbin/sendmail seuemail@teste.com</pre>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/code/cipa_t1/configurar_sendgrid.php" class="btn btn-success">📧 Configurar SendGrid</a>
            <a href="/code/cipa_t1/configurar_email.php" class="btn btn-warning">⚙️ Configurar Gmail</a>
            <a href="/code/cipa_t1/" class="btn">🏠 Voltar ao Sistema</a>
        </div>
    </div>
</body>
</html>
