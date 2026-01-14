<?php
    require_once __DIR__ . "/../models/Eleicao.php";
    require_once __DIR__ . "/../models/Documento.php";
    require_once __DIR__ . "/../repositories/DocumentoDAO.php";
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../utils/Util.php";

    class EleicaoController {
        private ?DocumentoDAO $documentoDAO;
        private ?EleicaoDAO $eleicaoDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->documentoDAO = new DocumentoDAO();
            $this->eleicaoDAO = new EleicaoDAO();
            
            // Verificar e finalizar eleições que passaram da data automaticamente
            $this->verificarEleicoesExpiradas();
        }

        private function verificarEleicoesExpiradas() {
            try {
                // Buscar eleições ABERTAS que já passaram da data de término
                $sql = "SELECT id_eleicao FROM eleicao 
                        WHERE status_eleicao = 'ABERTA' 
                        AND data_fim_eleicao < CURDATE()";
                
                $stmt = $this->eleicaoDAO->pegarConexao()->prepare($sql);
                $stmt->execute();
                $eleicoesExpiradas = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Finalizar cada eleição expirada
                foreach ($eleicoesExpiradas as $idEleicao) {
                    $this->eleicaoDAO->atualizarStatus($idEleicao, 'FINALIZADA');
                    error_log("Eleição ID $idEleicao finalizada automaticamente (data expirada)");
                }
            } catch (Exception $e) {
                error_log("Erro ao verificar eleições expiradas: " . $e->getMessage());
            }
        }

        public function inserirEleicao($requisicao) {
            if ($requisicao === "GET") {
                // Verificar se já existe eleição (aberta ou finalizada recentemente)
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.data_fim_eleicao >= CURDATE() - INTERVAL 7 DAY
                        ORDER BY e.data_fim_eleicao DESC
                        LIMIT 1";
                
                $stmt = $this->eleicaoDAO->pegarConexao()->prepare($sql);
                $stmt->execute();
                $eleicaoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($eleicaoExistente) {
                    // Verificar se está finalizada por data
                    $hoje = date('Y-m-d');
                    $dataFim = date('Y-m-d', strtotime($eleicaoExistente['data_fim_eleicao']));
                    $estaFinalizada = $dataFim <= $hoje;
                    
                    // Adicionar informação de status
                    $eleicaoExistente['status_real'] = $estaFinalizada ? 'FINALIZADA' : $eleicaoExistente['status_eleicao'];
                    
                    $_SESSION['eleicao_anterior'] = $eleicaoExistente;
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                    return;
                }
                
                // Buscar todos os documentos
                $documentos = $this->documentoDAO->buscarTodos();
                
                // Converter para objetos Documento
                if (!empty($documentos)) {
                    $documentos = Util::converterArrayDoc($documentos);
                } else {
                    $documentos = [];
                }
                
                $_SESSION["documentos"] = $documentos;
                include "./views/eleicao/cadastrar.php";
            }

            if ($requisicao === "POST") {
                // Verificar se deve apagar eleição anterior
                if (isset($_POST['confirmar_apagar']) && $_POST['confirmar_apagar'] === 'sim') {
                    $eleicaoAnterior = $_SESSION['eleicao_anterior'] ?? null;
                    if ($eleicaoAnterior && ($eleicaoAnterior['status_real'] ?? $eleicaoAnterior['status_eleicao']) !== 'FINALIZADA') {
                        // Só apaga se não for finalizada
                        $idEleicaoAnterior = $eleicaoAnterior['id_eleicao'];
                        if ($idEleicaoAnterior) {
                            $this->eleicaoDAO->deletar($idEleicaoAnterior);
                        }
                    }
                    // Se for finalizada, não apaga (mantém para consulta)
                }
                
                // Validar dados recebidos
                if (empty($_POST["idDocumento"]) || empty($_POST["dataInicioEleicao"]) || 
                    empty($_POST["dataFimEleicao"])) {
                    $_SESSION['erro_eleicao'] = "Todos os campos são obrigatórios.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                    return;
                }

                // Buscar documento relacionado
                $respostaDocumento = $this->documentoDAO->buscarPorId($_POST["idDocumento"]);
                
                if (!$respostaDocumento) {
                    $_SESSION['erro_eleicao'] = "Documento não encontrado.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                    return;
                }

                // Criar objeto Documento
                $documento = new Documento(
                    $respostaDocumento['data_inicio_documento'],
                    $respostaDocumento['data_fim_documento'],
                    $respostaDocumento['titulo_documento'],
                    $respostaDocumento['tipo_documento'],
                    $respostaDocumento['pdf_documento'] ?? '',
                    $respostaDocumento['data_hora_documento'] ?? '',
                    $respostaDocumento['id_documento']
                );
                
                // Criar objeto Eleicao
                $eleicao = new Eleicao(
                    $_POST["dataInicioEleicao"],
                    $_POST["dataFimEleicao"],
                    "ABERTA", // Status sempre ABERTA ao criar
                    $documento
                );

                // Inserir eleição
                $resposta = $this->eleicaoDAO->inserir($eleicao);

                if ($resposta) {
                    unset($_SESSION['erro_eleicao']);
                    $_SESSION['sucesso_eleicao'] = "Eleição criada com sucesso!";
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao cadastrar eleição. Tente novamente.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                }
            }
        }

        public function fecharEleicao($requisicao) {
            if ($requisicao === "POST") {
                if (!isset($_POST['idEleicao']) || empty($_POST['idEleicao'])) {
                    $_SESSION['erro_eleicao'] = "Eleição não especificada.";
                    header("Location: /code/cipa_t1/");
                    exit;
                }

                $idEleicao = $_POST['idEleicao'];
                
                // Verificar se eleição existe
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                if (!$eleicao) {
                    $_SESSION['erro_eleicao'] = "Eleição não encontrada.";
                    header("Location: /code/cipa_t1/");
                    exit;
                }

                // Atualizar status para FINALIZADA
                $resultado = $this->eleicaoDAO->atualizarStatus($idEleicao, 'FINALIZADA');
                
                if ($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Eleição finalizada com sucesso! Agora você pode gerar a ata.";
                    header("Location: /code/cipa_t1/ata/listar");
                    exit;
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao finalizar eleição. Tente novamente.";
                    header("Location: /code/cipa_t1/");
                    exit;
                }
            }
        }
    }