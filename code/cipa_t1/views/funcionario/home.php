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

    <?php include __DIR__ . '/../../components/alerts.php'; ?>

    <?php
        // Buscar eleição ativa
        require_once __DIR__ . "/../../repositories/EleicaoDAO.php";
        require_once __DIR__ . "/../../repositories/CandidatoDAO.php";
        require_once __DIR__ . "/../../repositories/DocumentoDAO.php";
        require_once __DIR__ . "/../../utils/Util.php";

        $eleicaoDAO = new EleicaoDAO();
        $candidatoDAO = new CandidatoDAO();
        $documentoDAO = new DocumentoDAO();
        
        // Buscar eleição ativa (já com status atualizado no DAO)
        $eleicao = $eleicaoDAO->buscarEstatisticasEleicaoAtiva();
        
        // Se há eleição, buscar candidatos e documentos
        $candidatos = [];
        $documentos = [];
        if ($eleicao) {
            $candidatos = $candidatoDAO->buscarPorEleicao($eleicao['id_eleicao']);
            
            // Buscar documentos
            $documentosData = $documentoDAO->buscarTodos();
            if (!empty($documentosData)) {
                $documentos = Util::converterArrayDoc($documentosData);
            }
        }
    ?>

    <div class="container">
        <?php if (isset($_SESSION['sucesso_candidatura'])): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_candidatura']); ?>
            </div>
            <?php unset($_SESSION['sucesso_candidatura']); ?>
        <?php endif; ?>

        <?php if ($eleicao): ?>
            <div class="info-box">
                <h3>Eleição Ativa</h3>
                <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
                
                <?php if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1): ?>
                <div style="margin-top: 15px;">
                    <form method="post" action="/code/cipa_t1/eleicao/fechar" style="display: inline;">
                        <input type="hidden" name="idEleicao" value="<?php echo $eleicao['id_eleicao']; ?>">
                        <button type="submit" class="btn-link" style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; display: inline-block;" 
                                onclick="return confirm('Tem certeza que deseja finalizar esta eleição? Esta ação não poderá ser desfeita.')">
                            🔒 Finalizar Eleição
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                
                <?php 
                // Verificar se o funcionário já é candidato
                $funcionarioLogado = $_SESSION['funcionario_logado'];
                $idFuncionarioLogado = $funcionarioLogado['id_funcionario'];
                $jaCandidato = false;
                
                if (!empty($candidatos)) {
                    foreach ($candidatos as $candidato) {
                        if ($candidato['usuario_fk'] == $idFuncionarioLogado) {
                            $jaCandidato = true;
                            break;
                        }
                    }
                }
                ?>
                
                <?php if (!$jaCandidato): ?>
                <div style="margin-top: 15px;">
                    <a href="/code/cipa_t1/funcionario/candidatar-se" class="btn-link" style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
                        🎯 Candidatar-se
                    </a>
                </div>
                <?php else: ?>
                <div style="margin-top: 15px;">
                    <span style="background-color: #17a2b8; color: white; padding: 10px 20px; border-radius: 4px; display: inline-block;">
                        ✅ Você já é candidato nesta eleição
                    </span>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="background-color: #e3f2fd; border-left-color: #2196f3; padding: 15px 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center;">
                    <span style="font-size: 20px; margin-right: 12px;">📅</span>
                    <div>
                        <strong style="color: #1976d2; font-size: 16px;">Nenhuma eleição ativa</strong>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Não há eleições em andamento no momento. Aguarde uma nova eleição ser criada.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Documentos</h2>
        <?php if (!empty($documentos)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                           <!-- <th>Tipo</th>-->
                           <!--<th>Data Início</th>-->
                            <th>Data Fim</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($doc->getTituloDocumento()); ?></strong></td>
                                <!-- <td><?php echo htmlspecialchars($doc->getTipoDocumento()); ?></td> -->
                                <!--<td><?php echo date('d/m/Y', strtotime($doc->getDataInicioDocumento())); ?></td>-->
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
                            <!--<th>Cargo</th>-->
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><strong style="font-size: 1.2em; color: #1e3a5f;"><?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong></td>
                                <td><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></td>
                                <!--<td><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></td>-->
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
