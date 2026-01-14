<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar ATA - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">📊</div>
        <div class="header-title">
            <h1>Gerar ATA de Resultados</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>

        <?php if (isset($_SESSION['mensagem_eleicao_ativa'])): ?>
            <div class="alert alert-info" style="background-color: #fff3cd; border-left-color: #ffc107; padding: 15px 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center;">
                    <span style="font-size: 20px; margin-right: 12px;">📅</span>
                    <div>
                        <strong style="color: #856404; font-size: 16px;">Eleição em Andamento</strong>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                            A ata da eleição "<strong><?php echo htmlspecialchars($_SESSION['mensagem_eleicao_ativa']['titulo']); ?></strong>" 
                            será gerada após a data de término (<strong><?php echo htmlspecialchars($_SESSION['mensagem_eleicao_ativa']['data_fim']); ?></strong>).
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['eleicoes_finalizadas'])): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['eleicoes_finalizadas'] as $eleicao): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></td>
                                <td><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></td>
                                <td>
                                    <a href="/code/cipa_t1/ata/gerar?eleicao=<?php echo $eleicao['id_eleicao']; ?>" class="btn-link" style="padding: 5px 15px; font-size: 0.9em;">Gerar ATA</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Nenhuma eleição finalizada encontrada.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
