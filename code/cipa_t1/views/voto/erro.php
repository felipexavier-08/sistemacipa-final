<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro na Votação</title>
</head>
<body>

    <h1>Erro na Votação</h1>
    
    <?php if (isset($_SESSION['erro_voto'])): ?>
        <div style="background-color: rgba(255, 0, 0, 0.2); padding: 20px; border-radius: 5px; margin: 20px 0; border: 2px solid #4b5c49;">
            <p style="font-size: 1.2em;"><?php echo htmlspecialchars($_SESSION['erro_voto']); ?></p>
        </div>
    <?php endif; ?>

    <a href="/code/cipa_t1/">Voltar para Página Inicial</a>

</body>
</html>
