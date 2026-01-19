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
            <?php if (!empty($_SESSION['funcionario_logado']['cod_voto_funcionario']) && $_SESSION['funcionario_logado']['adm_funcionario'] != 1): ?>
                <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; margin-right: 10px;">
                    🗳 Código: <?php echo htmlspecialchars($_SESSION['funcionario_logado']['cod_voto_funcionario']); ?>
                </span>
            <?php endif; ?>
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

        <!-- Botão Imprimir Comprovante -->
        <?php if (isset($_SESSION['comprovante_voto'])): ?>
            <div class="alert alert-success" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <h4 style="margin: 0 0 10px 0; color: #155724;">🗳️ Voto Registrado com Sucesso!</h4>
                        <p style="margin: 0; color: #155724;">Seu voto foi registrado. Imprima seu comprovante quando desejar.</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="/code/cipa_t1/voto/sucesso" class="btn-link" style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;">
                            🖨️ Imprimir Comprovante
                        </a>
                        <form method="POST" action="/code/cipa_t1/limpar-comprovante" style="display: inline;">
                            <button type="submit" class="btn-link" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; display: inline-block; font-weight: bold; font-size: 0.9em;" 
                                    title="Limpar comprovante da sessão">
                                🗑️ Limpar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botão Permanente de Imprimir Comprovante -->
        <?php 
        // Verificar se o funcionário já votou na eleição atual
        $funcionarioLogado = $_SESSION['funcionario_logado'];
        $idFuncionarioLogado = $funcionarioLogado['id_funcionario'];
        $jaVotou = false;
        
        if ($eleicao) {
            require_once __DIR__ . "/../../repositories/VotoDAO.php";
            $votoDAO = new VotoDAO();
            $jaVotou = $votoDAO->funcionarioJaVotou($idFuncionarioLogado, $eleicao['id_eleicao']);
        }
        
        // Mostrar botão se já votou e não há comprovante na sessão
        if ($jaVotou && !isset($_SESSION['comprovante_voto'])): 
        ?>
            <div class="alert alert-info" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <h4 style="margin: 0 0 10px 0; color: #0c5460;">🗳️ Você já votou nesta eleição!</h4>
                        <p style="margin: 0; color: #0c5460;">Clique abaixo para imprimir seu comprovante de voto.</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="/code/cipa_t1/voto/reimprimir-comprovante" class="btn-link" style="background-color: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;">
                            🖨️ Imprimir Comprovante
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($eleicao): ?>
            <!-- Botão Gerenciar fora do card -->
            <?php if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1): ?>
                <!-- Card clicável para admins -->
                <a href="/code/cipa_t1/eleicao/gerenciar" class="info-box-link" style="text-decoration: none; display: block; margin-bottom: 20px;">
                    <div class="info-box" style="position: relative; cursor: pointer; transition: all 0.3s ease;">
                        <div style="position: absolute; top: 10px; right: 10px; background-color: #007bff; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8em; font-weight: bold;">
                            ⚙️ Gerenciar
                        </div>
                        <h3>Eleição Ativa</h3>
                        <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                        <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                        <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
                        
                        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                            <form method="post" action="/code/cipa_t1/eleicao/fechar" style="display: inline;" onclick="event.stopPropagation();">
                                <input type="hidden" name="idEleicao" value="<?php echo $eleicao['id_eleicao']; ?>">
                                <button type="submit" class="btn-link" style="background-color: #dc3545; color: white; padding:10px 20px; border: none; border-radius:4px; cursor: pointer; display: inline-block; font-weight: bold;" 
                                        onclick="return confirm('Tem certeza que deseja finalizar esta eleição? Esta ação não poderá ser desfeita!')">
                                    🔒 Finalizar Eleição
                                </button>
                            </form>
                        </div>
                    </div>
                </a>
            <?php else: ?>
                <!-- Card normal para funcionários -->
                <div class="info-box">
                    <h3>Eleição Ativa</h3>
                    <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                    <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                    <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
                    
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
            <?php endif; ?>
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
