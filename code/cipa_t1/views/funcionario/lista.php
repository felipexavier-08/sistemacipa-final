<?php
    $funcionarios = $_SESSION["funcionarios"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">👥</div>
        <div class="header-title">
            <h1>Lista de Funcionários</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/cadastrar" class="btn-link">Novo Funcionário</a>
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['sucesso_funcionario'])): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_funcionario']); ?>
            </div>
            <?php unset($_SESSION['sucesso_funcionario']); ?>
        <?php endif; ?>
        
        <div class="table-container">
            <?php if (!empty($funcionarios) && is_array($funcionarios)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Sobrenome</th>
                            <th>Data de Nascimento</th>
                            <th>Data de Contratação</th>
                            <th>Ativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <?php
                                $id = $f->getIdFuncionario();
                                $nome = $f->getNomeFuncionario();
                                $sobrenome = $f->getSobrenomeFuncionario();
                                $dataNasc = $f->getDataNascimentoFuncionario();
                                $dataContr = $f->getDataContratacaoFuncionario();
                                $ativo = $f->getAtivoFuncionario();
                                
                                $dataNascFormatada = $dataNasc ? date('d/m/Y', strtotime($dataNasc)) : '-';
                                $dataContrFormatada = $dataContr ? date('d/m/Y', strtotime($dataContr)) : '-';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id); ?></td>
                                <td><?php echo htmlspecialchars($nome); ?></td>
                                <td><?php echo htmlspecialchars($sobrenome); ?></td>
                                <td><?php echo $dataNascFormatada; ?></td>
                                <td><?php echo $dataContrFormatada; ?></td>
                                <td><?php echo ($ativo == 1) ? 'Sim' : 'Não'; ?></td>
                                <td>
                                    <a href="/code/cipa_t1/funcionario/editar?id=<?php echo $id; ?>" class="btn-link" style="padding: 5px 10px; font-size: 0.9em; margin-right: 5px;">Editar</a>
                                    <a href="/code/cipa_t1/funcionario/deletar?id=<?php echo $id; ?>" class="btn-link btn-secondary" style="padding: 5px 10px; font-size: 0.9em;" onclick="return confirm('Tem certeza que deseja deletar este funcionário?');">Deletar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum funcionário encontrado.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>