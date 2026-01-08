<?php
    $documentos = $_SESSION["documentos"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Eleição - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Cadastrar Eleição</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>Dados da Eleição</h1>

            <?php if (isset($_SESSION['erro_eleicao'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_eleicao']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/eleicao/cadastrar">
                <input type="hidden" name="idEleicao" value="">

                <label for="idDocumento">Edital Relacionado:</label>
                <select id="idDocumento" name="idDocumento" required>
                    <option value="">Selecione um edital...</option>
                    <?php if (!empty($documentos) && is_array($documentos)): ?>
                        <?php foreach ($documentos as $doc): ?>
                            <?php
                                $idDoc = is_object($doc) ? $doc->getIdDocumento() : ($doc['id_documento'] ?? $doc['idDocumento'] ?? '');
                                $tituloDoc = is_object($doc) ? $doc->getTituloDocumento() : ($doc['titulo_documento'] ?? $doc['tituloDocumento'] ?? '');
                            ?>
                            <option value="<?php echo htmlspecialchars($idDoc); ?>">
                                <?php echo htmlspecialchars($tituloDoc); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Nenhum documento encontrado. Cadastre um documento primeiro.</option>
                    <?php endif; ?>
                </select>
                <?php if (empty($documentos)): ?>
                    <small>Nenhum documento disponível. <a href="/code/cipa_t1/documento/cadastrar">Cadastre um documento</a> primeiro.</small>
                <?php endif; ?>

                <label for="dataInicioEleicao">Data de Início:</label>
                <input type="date" id="dataInicioEleicao" name="dataInicioEleicao" required>

                <label for="dataFimEleicao">Data de Término:</label>
                <input type="date" id="dataFimEleicao" name="dataFimEleicao" required>

                <label for="statusEleicao">Status da Eleição:</label>
                <select id="statusEleicao" name="statusEleicao" required>
                    <option value="ABERTA">Aberta</option>
                    <option value="FECHADA">Fechada</option>
                    <option value="EM_ANDAMENTO">Em Andamento</option>
                    <option value="FINALIZADA">Finalizada</option>
                </select>
                <small>Selecione "Aberta" para permitir votação</small>

                <button type="submit">Salvar Eleição</button>
            </form>
        </div>
    </div>

</body>
</html>