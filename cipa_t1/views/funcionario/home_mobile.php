<?php
    // Inicia a sessão para acessar as variáveis
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // Carregar dados necessários para a view do funcionário
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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/mobile.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CIPA - Área do Funcionário</title>
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

    <div class="container">
        <?php if ($eleicao): ?>
            <div class="info-box">
                <h3>Eleição Ativa</h3>
                <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
            </div>

            <?php if (!empty($candidatos)): ?>
                <div class="info-box">
                    <h3>Candidatos</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th class="desktop-only">Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidatos as $candidato): ?>
                                    <tr>
                                        <td><strong style="font-size: 1.2em; color: #1e3a5f;"><?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></td>
                                        <td><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></td>
                                        <td class="desktop-only">
                                            <?php if (!empty($candidato['foto_candidato'])): ?>
                                                <img src="/code/cipa_t1/<?php echo htmlspecialchars($candidato['foto_candidato']); ?>" 
                                                     alt="Foto" class="foto-candidato">
                                            <?php else: ?>
                                                <span class="no-foto">Sem foto</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="/code/cipa_t1/voto/votar" class="btn-votar">Votar Agora</a>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum candidato cadastrado para esta eleição.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                Não há eleição ativa no momento.
            </div>
        <?php endif; ?>

        <?php if (!empty($documentos)): ?>
            <div class="info-box">
                <h3>Documentos</h3>
                <div class="table-container">
                    <table>
                        <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th class="desktop-only">Visualizar</th>
                                </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($doc->getTituloDocumento()); ?></strong></td>
                                        <td><?php echo htmlspecialchars($doc->getTipoDocumento()); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($doc->getDataInicioDocumento())); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($doc->getDataFimDocumento())); ?></td>
                                        <td class="desktop-only">
                                            <?php if (!empty($doc->getPdfDocumento())): ?>
                                                <a href="/code/cipa_t1/<?php echo htmlspecialchars($doc->getPdfDocumento()); ?>" 
                                                   target="_blank" 
                                                   style="color: #007bff; text-decoration: none;">Ver PDF</a>
                                            <?php else: ?>
                                                <span style="color: #666;">Não disponível</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
