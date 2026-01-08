<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voto Registrado - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Voto Registrado</h1>
            <p>Sistema CIPA</p>
        </div>
    </div>

    <div class="container">
        <div class="form-container" style="max-width: 600px; text-align: center;">
            <div class="alert alert-success" style="font-size: 1.2em;">
                <strong>Sucesso!</strong><br>
                Seu voto foi registrado com sucesso. Obrigado por participar!
            </div>

            <?php 
                $urlRetorno = "/code/cipa_t1/";
                if (isset($_SESSION['funcionario_logado']) && $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                    $urlRetorno = "/code/cipa_t1/funcionario/home";
                }
            ?>
            <a href="<?php echo $urlRetorno; ?>" class="btn-link" style="font-size: 1.1em; padding: 12px 30px;">Voltar para Página Inicial</a>
        </div>
    </div>

</body>
</html>
