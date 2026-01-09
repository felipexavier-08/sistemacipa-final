<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial - Funcionário</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">👤</div>
        <div class="header-title">
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['funcionario_logado']['nome_funcionario']); ?>!</h1>
            <p>Sistema CIPA - Área do Funcionário</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/logout">Sair</a>
        </div>
    </div>

    <?php
        // Buscar eleição ativa
        require_once __DIR__ . "/../../repositories/EleicaoDAO.php";
        require_once __DIR__ . "/../../repositories/CandidatoDAO.php";
        require_once __DIR__ . "/../../repositories/DocumentoDAO.php";
        require_once __DIR__ . "/../../utils/Util.php";

        $eleicaoDAO = new EleicaoDAO();
        $candidatoDAO = new CandidatoDAO();
        $documentoDAO = new DocumentoDAO();

        $idEleicao = $eleicaoDAO->buscarEleicaoAberta();
        $eleicao = null;
        $candidatos = [];
        $documentos = [];

        if ($idEleicao) {
            $eleicao = $eleicaoDAO->buscarPorId($idEleicao);
            $candidatos = $candidatoDAO->buscarPorEleicao($idEleicao);
            
            // Buscar documentos
            $documentosData = $documentoDAO->buscarTodos();
            if (!empty($documentosData)) {
                $documentos = Util::converterArrayDoc($documentosData);
            }
        }
    ?>

    <div class="container">
        <?php if ($eleicao): ?>
            <div class="info-box">
                <h3>Eleição Ativa</h3>
                <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Não há eleição ativa no momento.
            </div>
        <?php endif; ?>

        <h2>Documentos</h2>
        <?php if (!empty($documentos)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($doc->getTituloDocumento()); ?></strong></td>
                                <td><?php echo htmlspecialchars($doc->getTipoDocumento()); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($doc->getDataInicioDocumento())); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($doc->getDataFimDocumento())); ?></td>
                                <td>
                                    <?php if (!empty($doc->getPdfDocumento())): ?>
                                        <a href="/code/cipa_t1/<?php echo htmlspecialchars($doc->getPdfDocumento()); ?>" target="_blank" class="btn-link" style="padding: 5px 10px; font-size: 0.9em;">Ver PDF</a>
                                    <?php else: ?>
                                        <em>Não disponível</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Nenhum documento disponível.
            </div>
        <?php endif; ?>

        <?php if ($eleicao && !empty($candidatos)): ?>
            <h2 style="margin-top: 40px;">Candidatos</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><strong style="font-size: 1.2em; color: #1e3a5f;"><?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong></td>
                                <td><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></td>
                                <td><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></td>
                                <td>
                                    <?php if (!empty($candidato['foto_candidato'])): ?>
                                        <img src="/code/cipa_t1/<?php echo htmlspecialchars($candidato['foto_candidato']); ?>" 
                                             alt="Foto" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #ddd;">
                                    <?php else: ?>
                                        <em>Sem foto</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="/code/cipa_t1/voto/votar" class="btn-link" style="font-size: 1.2em; padding: 15px 40px;">Votar</a>
            </div>
        <?php elseif ($eleicao): ?>
            <h2 style="margin-top: 40px;">Candidatos</h2>
            <div class="alert alert-info">
                Nenhum candidato cadastrado para esta eleição.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
