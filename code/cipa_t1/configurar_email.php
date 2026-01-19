<?php
/**
 * Script de configuração e instalação do PHPMailer
 * Guia completo para configurar envio de emails
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Email - Sistema CIPA</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Configuração de Email - Sistema CIPA</h1>
        
        <?php
        // Verificar se PHPMailer está instalado
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            echo "<div class='success'>✅ PHPMailer está instalado via Composer</div>";
            require_once __DIR__ . '/vendor/autoload.php';
        } elseif (file_exists(__DIR__ . '/lib/PHPMailer.php')) {
            echo "<div class='success'>✅ PHPMailer está instalado manualmente</div>";
            require_once __DIR__ . '/lib/PHPMailer.php';
            require_once __DIR__ . '/lib/SMTP.php';
            require_once __DIR__ . '/lib/Exception.php';
        } else {
            echo "<div class='warning'>⚠️ PHPMailer não encontrado. Vamos instalar agora...</div>";
        }
        
        // Processar instalação
        if (isset($_POST['install_phpmailer'])) {
            echo "<div class='info'>📦 Instalando PHPMailer...</div>";
            
            // Criar diretório vendor se não existir
            if (!file_exists(__DIR__ . '/vendor')) {
                mkdir(__DIR__ . '/vendor', 0755, true);
            }
            
            // Download do PHPMailer (versão leve)
            $phpmailer_zip = __DIR__ . '/phpmailer.zip';
            $downloaded = file_put_contents($phpmailer_zip, fopen('https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip', 'r'));
            
            if ($downloaded) {
                echo "<div class='success'>✅ PHPMailer baixado com sucesso!</div>";
                
                // Extrair (simplificado - em produção use unzip)
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive();
                    if ($zip->open($phpmailer_zip) === TRUE) {
                        $zip->extractTo(__DIR__ . '/vendor/');
                        $zip->close();
                        echo "<div class='success'>✅ PHPMailer extraído com sucesso!</div>";
                        
                        // Renomear pasta
                        if (file_exists(__DIR__ . '/vendor/PHPMailer-6.9.1')) {
                            rename(__DIR__ . '/vendor/PHPMailer-6.9.1', __DIR__ . '/vendor/PHPMailer');
                        }
                        
                        unlink($phpmailer_zip);
                    } else {
                        echo "<div class='error'>❌ Erro ao extrair PHPMailer</div>";
                    }
                } else {
                    echo "<div class='warning'>⚠️ ZipArchive não disponível. Instale manualmente.</div>";
                }
            } else {
                echo "<div class='error'>❌ Erro ao baixar PHPMailer</div>";
            }
        }
        
        // Testar configuração atual
        if (isset($_POST['test_email'])) {
            require_once __DIR__ . '/utils/EmailService.php';
            
            $emailService = new EmailService();
            $testEmail = $_POST['test_email'];
            $resultado = $emailService->testarConfiguracao($testEmail);
            
            if ($resultado) {
                echo "<div class='success'>✅ Email de teste enviado para $testEmail!</div>";
            } else {
                echo "<div class='error'>❌ Falha ao enviar email para $testEmail</div>";
            }
        }
        ?>
        
        <div class="step">
            <h2>📋 Opções de Configuração</h2>
            
            <h3>Opção 1: Gmail (Gratuito e Recomendado)</h3>
            <p>Use uma conta Gmail para enviar emails. É gratuito e funciona bem.</p>
            
            <h4>Configuração Gmail:</h4>
            <ol>
                <li>Crie uma conta Gmail (ou use uma existente)</li>
                <li>Ative a "Verificação em duas etapas"</li>
                <li>Crie uma "Senha de aplicativo":
                    <ul>
                        <li>Vá para: <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></li>
                        <li>Selecione "Outro (nome personalizado)"</li>
                        <li>Digite "Sistema CIPA"</li>
                        <li>Copie a senha gerada (16 caracteres)</li>
                    </ul>
                </li>
                <li>Atualize o arquivo <strong>EmailService.php</strong> com seus dados:</li>
            </ol>
            
            <pre>
// Em EmailService.php, método getEmailConfig():
$config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'secure' => 'tls',
    'username' => 'seuemail@gmail.com',        // Seu email Gmail
    'password' => 'suasenhaapp1234',            // Senha de app (16 chars)
    'from_email' => 'cipa@sistema.com',
    'from_name' => 'Sistema CIPA'
];
            </pre>
            
            <h3>Opção 2: Mailtrap (Teste Grátis)</h3>
            <p>Para testes, use o Mailtrap. É gratuito para desenvolvimento.</p>
            
            <ul>
                <li>Cadastre-se em: <a href="https://mailtrap.io" target="_blank">https://mailtrap.io</a></li>
                <li>Crie uma caixa postal SMTP</li>
                <li>Use as credenciais no EmailService.php</li>
            </ul>
            
            <h3>Opção 3: SendGrid (Grátis até 100 emails/dia)</h3>
            <p>Serviço profissional com plano gratuito generoso.</p>
            
            <ul>
                <li>Cadastre-se em: <a href="https://sendgrid.com" target="_blank">https://sendgrid.com</a></li>
                <li>Plano gratuito: 100 emails/dia</li>
                <li>Configure SMTP no EmailService.php</li>
            </ul>
        </div>
        
        <div class="step">
            <h2>🔧 Instalação Automática</h2>
            
            <?php if (!file_exists(__DIR__ . '/vendor/autoload.php')): ?>
                <form method="post">
                    <button type="submit" name="install_phpmailer" class="btn btn-success">📦 Instalar PHPMailer Automaticamente</button>
                </form>
                <p><small>Isso baixará e instalará o PHPMailer no seu sistema.</small></p>
            <?php endif; ?>
        </div>
        
        <div class="step">
            <h2>🧪 Testar Configuração</h2>
            
            <form method="post">
                <label>Email para teste: 
                    <input type="email" name="test_email" placeholder="seuemail@teste.com" required style="width: 300px; padding: 5px;">
                </label>
                <button type="submit" name="test_email" class="btn">📧 Enviar Email de Teste</button>
            </form>
        </div>
        
        <div class="step">
            <h2>📝 Arquivos de Configuração</h2>
            
            <h3>1. EmailService.php</h3>
            <p>Já criado em <code>/utils/EmailService.php</code></p>
            
            <h3>2. .env (Opcional - para produção)</h3>
            <pre>
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_SECURE=tls
EMAIL_USERNAME=seuemail@gmail.com
EMAIL_PASSWORD=suasenhaapp
EMAIL_FROM=cipa@sistema.com
EMAIL_FROM_NAME=Sistema CIPA
            </pre>
        </div>
        
        <div class="step">
            <h2>🚀 Próximos Passos</h2>
            
            <ol>
                <li>✅ Escolha um serviço de email (Gmail recomendado)</li>
                <li>✅ Configure suas credenciais no EmailService.php</li>
                <li>✅ Teste o envio com o formulário acima</li>
                <li>✅ Cadastre um novo funcionário para testar automaticamente</li>
            </ol>
            
            <p><strong>Após configurar:</strong></p>
            <ul>
                <li>Todos os novos funcionários receberão código de voto por email</li>
                <li>O sistema funcionará em desenvolvimento e produção</li>
                <li>Emails terão design profissional e HTML</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/code/cipa_t1/" class="btn">🏠 Voltar ao Sistema</a>
        </div>
    </div>
</body>
</html>
