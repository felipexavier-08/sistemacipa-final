<?php
    $eleicaoAtiva = $_SESSION["eleicao_ativa"] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eleição Ativa - Sistema CIPA</title>
</head>
<body>
    <div class="header">
        <div class="header-icon">🗳️</div>
        <div class="header-title">
            <h1>Gerenciar Eleição Ativa</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/listar" class="btn-link">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['sucesso_eleicao'])): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_eleicao']); ?>
            </div>
            <?php unset($_SESSION['sucesso_eleicao']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro_eleicao'])): ?>
            <div class="alert alert-error">
                <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_eleicao']); ?>
            </div>
            <?php unset($_SESSION['erro_eleicao']); ?>
        <?php endif; ?>

        <?php if ($eleicaoAtiva && is_array($eleicaoAtiva)): ?>
            <div class="form-container">
                <h2>📊 Dados da Eleição Ativa</h2>
                
                <div class="info-box">
                    <h3>Informações da Eleição</h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($eleicaoAtiva['id_eleicao'] ?? 'N/A'); ?></p>
                    <p><strong>Data de Início:</strong> <?php echo ($eleicaoAtiva['data_inicio_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicaoAtiva['data_inicio_eleicao'])) : 'N/A'; ?></p>
                    <p><strong>Data de Término:</strong> <?php echo ($eleicaoAtiva['data_fim_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicaoAtiva['data_fim_eleicao'])) : 'N/A'; ?></p>
                    <p><strong>Status:</strong> <span class="status-ativo"><?php echo htmlspecialchars($eleicaoAtiva['status_eleicao'] ?? 'N/A'); ?></span></p>
                    <p><strong>Total de Candidatos:</strong> <?php echo htmlspecialchars($eleicaoAtiva['total_candidatos'] ?? '0'); ?></p>
                    <p><strong>Status da Votação:</strong> 
                        <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 1): ?>
                            <span class="status-ativo" style="background: #28a745;">✅ AUTORIZADA</span>
                        <?php else: ?>
                            <span class="status-inativo" style="background: #ffc107; color: #856404;">⏳ NÃO AUTORIZADA</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="action-buttons">
                    <form method="POST" action="/code/cipa_t1/eleicao/estender" style="display: inline-block; margin-right: 10px;">
                        <label for="novaDataFim">Nova Data de Término:</label>
                        <input type="date" id="novaDataFim" name="novaDataFim" 
                               min="<?php echo date('Y-m-d'); ?>" 
                               value="<?php echo ($eleicaoAtiva['data_fim_eleicao'] ?? null) ? date('Y-m-d', strtotime($eleicaoAtiva['data_fim_eleicao'])) : date('Y-m-d'); ?>" required>
                        <button type="submit" class="btn-link">📅 Estender Período</button>
                    </form>

                    <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 0): ?>
                        <form method="POST" action="/code/cipa_t1/eleicao/autorizar-votacao" style="display: inline-block; margin-right: 10px;">
                            <button type="submit" class="btn-link" style="background-color: #28a745; color: white;"
                                    onclick="return confirm('Tem certeza que deseja autorizar a votação? Isso encerrará o período de candidaturas e liberará a votação para todos os funcionários!')">
                                🗳️ Autorizar Votação
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/code/cipa_t1/eleicao/bloquear-votacao" style="display: inline-block; margin-right: 10px;">
                            <button type="submit" class="btn-link btn-secondary"
                                    onclick="return confirm('Tem certeza que deseja bloquear a votação? Isso reabrirá o período de candidaturas e bloqueará a votação!')">
                                🚫 Bloquear Votação
                            </button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" action="/code/cipa_t1/eleicao/finalizar" style="display: inline-block;">
                        <button type="submit" class="btn-link btn-danger" 
                                onclick="return confirm('Tem certeza que deseja finalizar esta eleição? Esta ação não poderá ser desfeita!')">
                            🔒 Finalizar Eleição
                        </button>
                    </form>
                </div>

                <div class="info-box" style="margin-top: 20px;">
                    <h3>📝 Controle de Votação</h3>
                    <div style="background: <?php echo (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 1) ? '#d4edda' : '#fff3cd'; ?>; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 0): ?>
                            <h4 style="color: #856404; margin-top: 0;">⏳ Votação NÃO Autorizada</h4>
                            <ul style="color: #856404;">
                                <li>Funcionários podem se candidatar</li>
                                <li>Funcionários NÃO podem votar</li>
                                <li>Candidatos NÃO são visíveis</li>
                            </ul>
                        <?php else: ?>
                            <h4 style="color: #155724; margin-top: 0;">✅ Votação AUTORIZADA</h4>
                            <ul style="color: #155724;">
                                <li>Funcionários NÃO podem se candidatar</li>
                                <li>Funcionários podem votar</li>
                                <li>Candidatos são visíveis para votação</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-box" style="margin-top: 20px;">
                    <h3>📝 Ações Disponíveis</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>📅 Estender Período:</strong> Alterar a data de término da eleição para dar mais tempo para votação.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🗳️ Autorizar Votação:</strong> Iniciar o período de votação e encerrar as candidaturas.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🚫 Bloquear Votação:</strong> Reabrir período de candidaturas e bloquear votação.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🔒 Finalizar Eleição:</strong> Encerrar a eleição atual, marcando como FINALIZADA e bloqueando novas votações.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-container" style="margin-top: 30px;">
                <h2>👥 Cadastro Rápido de Funcionário</h2>
                <p style="margin-bottom: 15px; color: #666;">
                    Cadastre novos funcionários que poderão participar da eleição atual.
                </p>
                <a href="/code/cipa_t1/funcionario/cadastrar" class="btn-link" style="font-size: 1.1em; padding: 12px 24px;">
                    ➕ Cadastrar Novo Funcionário
                </a>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                <strong>Informação:</strong> Não há nenhuma eleição ativa no momento.
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="/code/cipa_t1/eleicao/cadastrar" class="btn-link" style="font-size: 1.1em; padding: 12px 24px;">
                    ➕ Criar Nova Eleição
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
