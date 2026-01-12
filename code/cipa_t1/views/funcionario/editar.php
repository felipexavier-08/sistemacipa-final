<?php
    $funcionario = $_SESSION['funcionario_editar'] ?? null;
    if (!$funcionario) {
        header("Location: /code/cipa_t1/funcionario/listar");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✏️</div>
        <div class="header-title">
            <h1>Editar Funcionário</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/listar">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>Dados do Funcionário</h1>
            <form method="post" action="/code/cipa_t1/funcionario/editar">
                <input type="hidden" name="idFuncionario" value="<?php echo $funcionario->getIdFuncionario(); ?>">

                <label for="nomeFuncionario">Nome:</label>
                <input type="text" id="nomeFuncionario" name="nomeFuncionario" value="<?php echo htmlspecialchars($funcionario->getNomeFuncionario()); ?>" required>

                <label for="sobrenomeFuncionario">Sobrenome:</label>
                <input type="text" id="sobrenomeFuncionario" name="sobrenomeFuncionario" value="<?php echo htmlspecialchars($funcionario->getSobrenomeFuncionario()); ?>" required>

                <label for="cpfFuncionario">CPF:</label>
                <input type="text" id="cpfFuncionario" name="cpfFuncionario" value="<?php echo htmlspecialchars($funcionario->getCpfFuncionario()); ?>" required maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="dataNascimentoFuncionario">Data de Nascimento:</label>
                <input type="date" id="dataNascimentoFuncionario" name="dataNascimentoFuncionario" value="<?php echo $funcionario->getDataNascimentoFuncionario(); ?>" required>

                <label for="dataContratacaoFuncionario">Data de Contratação:</label>
                <input type="date" id="dataContratacaoFuncionario" name="dataContratacaoFuncionario" value="<?php echo $funcionario->getDataContratacaoFuncionario(); ?>" required>

                <label for="telefoneFuncionario">Telefone:</label>
                <input type="tel" id="telefoneFuncionario" name="telefoneFuncionario" value="<?php echo htmlspecialchars($funcionario->getTelefoneFuncionario()); ?>" maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="matriculaFuncionario">Matrícula:</label>
                <input type="text" id="matriculaFuncionario" name="matriculaFuncionario" value="<?php echo htmlspecialchars($funcionario->getMatriculaFuncionario()); ?>">

                <label for="codigoVotoFuncionario">Código de Voto:</label>
                <input type="text" id="codigoVotoFuncionario" name="codigoVotoFuncionario" value="<?php echo htmlspecialchars($funcionario->getCodigoVotoFuncionario()); ?>">

                <label for="emailFuncionario">Email:</label>
                <input type="email" id="emailFuncionario" name="emailFuncionario" value="<?php echo htmlspecialchars($funcionario->getEmailFuncionario()); ?>" required>

                <label for="senhaFuncionario">Senha:</label>
                <input type="password" id="senhaFuncionario" name="senhaFuncionario" placeholder="Preencha apenas se quiser alterar">
                <small>Deixe em branco para manter a senha atual</small>

                <fieldset>
                    <legend>Configurações</legend>
                    <label for="ativoFuncionario" style="margin-top: 0;">
                        <input type="checkbox" id="ativoFuncionario" name="ativoFuncionario" value="1" <?php echo ($funcionario->getAtivoFuncionario() == 1) ? 'checked' : ''; ?>>
                        Funcionário Ativo
                    </label>

                    <label for="admFuncionario" style="margin-top: 10px;">
                        <input type="checkbox" id="admFuncionario" name="admFuncionario" value="1" <?php echo ($funcionario->getAdmFuncionario() == 1) ? 'checked' : ''; ?>>
                        Administrador
                    </label>
                </fieldset>

                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>

</body>
</html>