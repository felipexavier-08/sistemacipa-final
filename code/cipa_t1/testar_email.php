<?php
// Script de teste para envio de email
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Envio de Email</h1>";

// Verificar se a função mail() está disponível
if (!function_exists('mail')) {
    echo "<p style='color: red;'>❌ Função mail() não está disponível neste servidor</p>";
    echo "<p>Verifique se o serviço de email está configurado no seu ambiente XAMPP/LAMPP</p>";
} else {
    echo "<p style='color: green;'>✅ Função mail() está disponível</p>";
}

// Testar envio de email simples
$testEmail = $_POST['email'] ?? 'test@example.com';
$assunto = "Teste de Email - Sistema CIPA";
$mensagem = "Este é um email de teste do Sistema CIPA.\n\nSe você recebeu este email, o envio está funcionando corretamente.";
$headers = "From: Sistema CIPA <cipa@localhost>\r\n";

echo "<h2>Testando envio para: $testEmail</h2>";

$enviado = mail($testEmail, $assunto, $mensagem, $headers);

if ($enviado) {
    echo "<p style='color: green;'>✅ Email enviado com sucesso!</p>";
    echo "<p>Verifique sua caixa de entrada (e também a pasta de spam/lixeira)</p>";
} else {
    echo "<p style='color: red;'>❌ Falha ao enviar email</p>";
    echo "<p>Possíveis causas:</p>";
    echo "<ul>";
    echo "<li>Servidor de email não configurado no XAMPP/LAMPP</li>";
    echo "<li>Firewall bloqueando porta 25</li>";
    echo "<li>Configuração SMTP incorreta</li>";
    echo "</ul>";
}

// Mostrar configuração do PHP
echo "<h2>Configuração do PHP para Email</h2>";
echo "<p><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</p>";
echo "<p><strong>SMTP:</strong> " . ini_get('SMTP') . "</p>";
echo "<p><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</p>";

// Formulário para teste
echo "<h2>Testar com outro email</h2>";
echo "<form method='post'>";
echo "<label>Email para teste: <input type='email' name='email' value='$testEmail' required></label><br><br>";
echo "<input type='submit' value='Testar Envio'>";
echo "</form>";

// Instruções para configurar email no XAMPP/LAMPP
echo "<h2>Como Configurar Email no XAMPP/LAMPP</h2>";
echo "<ol>";
echo "<li>Abra o arquivo <strong>php.ini</strong> (geralmente em /opt/lampp/etc/php.ini)</li>";
echo "<li>Procure pelas seguintes linhas:</li>";
echo "<pre>";
echo "[mail function]";
echo "SMTP = localhost";
echo "smtp_port = 25";
echo "sendmail_path = /usr/sbin/sendmail -t -i";
echo "</pre>";
echo "<li>Instale e configure um servidor SMTP (como Postfix ou Sendmail)</li>";
echo "<li>Reinicie o Apache/XAMPP</li>";
echo "</ol>";
?>
