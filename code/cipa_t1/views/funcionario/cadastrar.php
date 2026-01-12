<?php
    
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">👤</div>
        <div class="header-title">
            <h1>Cadastrar Funcionário</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>Dados do Funcionário</h1>

            <form method="post" action="/code/cipa_t1/funcionario/cadastrar">
                <input type="hidden" name="idFuncionario" value="">

                <label for="nomeFuncionario">Nome:</label>
                <input type="text" id="nomeFuncionario" name="nomeFuncionario" required>

                <label for="sobrenomeFuncionario">Sobrenome:</label>
                <input type="text" id="sobrenomeFuncionario" name="sobrenomeFuncionario" required>

                <label for="cpfFuncionario">CPF:</label>
                <input type="text" id="cpfFuncionario" name="cpfFuncionario" required maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="dataNascimentoFuncionario">Data de Nascimento:</label>
                <input type="date" id="dataNascimentoFuncionario" name="dataNascimentoFuncionario" required>

                <label for="dataContratacaoFuncionario">Data de Contratação:</label>
                <input type="date" id="dataContratacaoFuncionario" name="dataContratacaoFuncionario" required>

                <label for="telefoneFuncionario">Telefone:</label>
                <input type="tel" id="telefoneFuncionario" name="telefoneFuncionario" maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="matriculaFuncionario">Matrícula:</label>
                <input type="text" id="matriculaFuncionario" name="matriculaFuncionario">

                <label for="codigoVotoFuncionario">Código de Voto:</label>
                <input type="text" id="codigoVotoFuncionario" name="codigoVotoFuncionario">

                <label for="emailFuncionario">Email:</label>
                <input type="email" id="emailFuncionario" name="emailFuncionario" required>

                <label for="senhaFuncionario">Senha:</label>
                <input type="password" id="senhaFuncionario" name="senhaFuncionario" required>

                <fieldset>
                    <legend>Configurações</legend>
                    <label for="ativoFuncionario" style="margin-top: 0;">
                        <input type="checkbox" id="ativoFuncionario" name="ativoFuncionario" value="1" checked>
                        Funcionário Ativo
                    </label>

                    <label for="admFuncionario" style="margin-top: 10px;">
                        <input type="checkbox" id="admFuncionario" name="admFuncionario" value="1">
                        Administrador
                    </label>
                </fieldset>

                <button type="submit">Salvar Funcionário</button>
            </form>
        </div>
    </div>

</body>
</html>