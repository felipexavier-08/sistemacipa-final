<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CIPA - Página Inicial</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Sistema CIPA</h1>
            <p>Comissão Interna de Prevenção de Acidentes</p>
        </div>
        <div class="header-actions">
            <span style="color: rgba(255,255,255,0.9);"><?php echo htmlspecialchars($_SESSION['funcionario_logado']['nome_funcionario']); ?></span>
            <a href="/code/cipa_t1/logout">Sair</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-section">
            <h1>Bem vindo!</h1>
            <p>Selecione uma das opções abaixo para gerenciar o sistema.</p>
        </div>

        <div class="cards-grid">
            <a href="/code/cipa_t1/funcionario/cadastrar" class="card">
                <div class="card-icon">👤+</div>
                <div class="card-title">Cadastrar Funcionário</div>
                <div class="card-description">Adicione novos funcionários ao sistema</div>
            </a>

            <a href="/code/cipa_t1/funcionario/cadastrar-por-matricula" class="card">
                <div class="card-icon">🔍</div>
                <div class="card-title">Buscar por Matrícula</div>
                <div class="card-description">Localize funcionário por matrícula e CPF</div>
            </a>

            <a href="/code/cipa_t1/funcionario/listar" class="card">
                <div class="card-icon">👥</div>
                <div class="card-title">Listar Funcionários</div>
                <div class="card-description">Visualize todos os funcionários cadastrados</div>
            </a>

            <a href="/code/cipa_t1/documento/cadastrar" class="card">
                <div class="card-icon">📄</div>
                <div class="card-title">Cadastrar Documento</div>
                <div class="card-description">Registre novos documentos no sistema</div>
            </a>

            <a href="/code/cipa_t1/documento/listar" class="card">
                <div class="card-icon">📚</div>
                <div class="card-title">Listar Documentos</div>
                <div class="card-description">Acesse os documentos registrados</div>
            </a>

            <a href="/code/cipa_t1/eleicao/cadastrar" class="card">
                <div class="card-icon">✓</div>
                <div class="card-title">Cadastrar Eleição</div>
                <div class="card-description">Configure uma nova eleição da CIPA</div>
            </a>

            <a href="/code/cipa_t1/candidato/cadastrar" class="card">
                <div class="card-icon">🎯</div>
                <div class="card-title">Cadastrar Candidato</div>
                <div class="card-description">Registre candidatos para a eleição</div>
            </a>

            <a href="/code/cipa_t1/voto/listar-candidatos" class="card">
                <div class="card-icon">📋</div>
                <div class="card-title">Listar Candidatos</div>
                <div class="card-description">Visualize os candidatos da eleição ativa</div>
            </a>

            <a href="/code/cipa_t1/ata/listar" class="card">
                <div class="card-icon">📊</div>
                <div class="card-title">Gerar ATA</div>
                <div class="card-description">Gere ATA de eleições finalizadas</div>
            </a>
        </div>
    </div>

</body>
</html>
