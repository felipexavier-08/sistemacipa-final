<?php
    $documentos = $_SESSION["documentos"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Documentos - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">📚</div>
        <div class="header-title">
            <h1>Lista de Documentos</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/documento/cadastrar" class="btn-link">Novo Documento</a>
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="table-container">
            <?php if (!empty($documentos) && is_array($documentos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $doc): ?>
                            <?php
                                if (is_object($doc)) {
                                    $id = $doc->getIdDocumento();
                                    $titulo = $doc->getTituloDocumento();
                                    $dataInicio = $doc->getDataInicioDocumento();
                                    $dataFim = $doc->getDataFimDocumento();
                                    $tipo = $doc->getTipoDocumento();
                                    $pdf = $doc->getPdfDocumento();
                                } else {
                                    $id = $doc['id_documento'] ?? $doc['IdDocumento'] ?? '';
                                    $titulo = $doc['tituloDocumento'] ?? $doc['TituloDocumento'] ?? '';
                                    $dataInicio = $doc['dataInicioDocumento'] ?? $doc['DataInicioDocumento'] ?? '';
                                    $dataFim = $doc['dataFimDocumento'] ?? $doc['DataFimDocumento'] ?? '';
                                    $tipo = $doc['tipoDocumento'] ?? $doc['TipoDocumento'] ?? '';
                                    $pdf = $doc['pdfDocumento'] ?? $doc['PdfDocumento'] ?? '';
                                }
                                
                                $dataInicioFormatada = $dataInicio ? date('d/m/Y', strtotime($dataInicio)) : '-';
                                $dataFimFormatada = $dataFim ? date('d/m/Y', strtotime($dataFim)) : '-';
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($titulo); ?></strong></td>
                                <td><?php echo $dataInicioFormatada; ?></td>
                                <td><?php echo $dataFimFormatada; ?></td>
                                <td><?php echo htmlspecialchars($tipo); ?></td>
                                <td>
                                    <?php if (!empty($pdf)): ?>
                                        <a href="/code/cipa_t1/<?php echo htmlspecialchars($pdf); ?>" target="_blank" class="btn-link" style="padding: 5px 10px; font-size: 0.9em;">Visualizar PDF</a>
                                    <?php else: ?>
                                        <em>Não disponível</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/code/cipa_t1/documento/deletar?id=<?php echo $id; ?>" class="btn-link" style="padding: 5px 10px; font-size: 0.9em; background-color: #dc3545;" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum documento cadastrado.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>