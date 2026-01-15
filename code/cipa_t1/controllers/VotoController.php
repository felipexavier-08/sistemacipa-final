<?php
    require_once __DIR__ . "/../repositories/VotoDAO.php";
    require_once __DIR__ . "/../repositories/CandidatoDAO.php";
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";
    require_once __DIR__ . "/../models/Voto.php";

    class VotoController {
        private ?VotoDAO $votoDAO;
        private ?CandidatoDAO $candidatoDAO;
        private ?EleicaoDAO $eleicaoDAO;
        private ?FuncionarioDAO $funcionarioDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->votoDAO = new VotoDAO();
            $this->candidatoDAO = new CandidatoDAO();
            $this->eleicaoDAO = new EleicaoDAO();
            $this->funcionarioDAO = new FuncionarioDAO();
        }

        public function votar($requisicao) {
            if ($requisicao === "GET") {
                // Buscar eleição aberta
                $idEleicao = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$idEleicao) {
                    $_SESSION['erro_voto'] = "Não há eleição aberta no momento.";
                    include "./views/voto/erro.php";
                    return;
                }

                // Buscar candidatos da eleição
                $candidatos = $this->candidatoDAO->buscarPorEleicao($idEleicao);
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                
                $_SESSION['candidatos_votacao'] = $candidatos;
                $_SESSION['eleicao_votacao'] = $eleicao;
                $_SESSION['id_eleicao_votacao'] = $idEleicao;
                
                include "./views/voto/votar.php";
            }

            if ($requisicao === "POST") {
                // Verificar se funcionário está logado
                if (!isset($_SESSION['funcionario_logado'])) {
                    $_SESSION['erro_voto'] = "Você precisa estar logado para votar.";
                    header("Location: /code/cipa_t1/login");
                    exit;
                }

                $idFuncionario = $_SESSION['funcionario_logado']['id_funcionario'];
                $idEleicao = $_POST['idEleicao'];
                
                // Verificar se eleição está realmente aberta
                $eleicaoStatus = $this->eleicaoDAO->buscarPorId($idEleicao);
                error_log("DEBUG: Verificando status da eleição - idEleicao: $idEleicao, status: " . ($eleicaoStatus['status_eleicao'] ?? 'NÃO ENCONTRADO') . ", data_fim: " . ($eleicaoStatus['data_fim_eleicao'] ?? 'NÃO ENCONTRADO'));

                if (!$eleicaoStatus || $eleicaoStatus['status_eleicao'] !== 'ABERTA') {
                    $_SESSION['erro_voto'] = "Esta eleição não está mais aberta para votação.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                // Verificar se já votou
                if ($this->votoDAO->funcionarioJaVotou($idFuncionario, $idEleicao)) {
                    $_SESSION['erro_voto'] = "Você já votou nesta eleição.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                // Processar voto
                if (isset($_POST['numeroCandidato']) && !empty($_POST['numeroCandidato'])) {
                    // Voto em candidato
                    $idCandidato = $this->candidatoDAO->buscarPorNumero($idEleicao, $_POST['numeroCandidato']);
                    
                    error_log("DEBUG: Buscando candidato - idEleicao: $idEleicao, numeroCandidato: " . $_POST['numeroCandidato'] . ", resultado: " . ($idCandidato ? 'ENCONTRADO' : 'NÃO ENCONTRADO'));
                    
                    if (!$idCandidato) {
                        $_SESSION['erro_voto'] = "Número de candidato inválido.";
                        header("Location: /code/cipa_t1/voto/votar");
                        exit;
                    }

                    // Registrar o voto
                    error_log("Tentando registrar voto - idFuncionario: $idFuncionario, idCandidato: $idCandidato");
                    $resultado = $this->votoDAO->registrarVoto($idFuncionario, $idCandidato);
                    error_log("Resultado do registro de voto: " . ($resultado ? 'SUCESSO' : 'FALHA'));
                } elseif (isset($_POST['tipoVoto']) && in_array($_POST['tipoVoto'], ['BRANCO', 'NULO'])) {
                    // Voto branco ou nulo
                    $resultado = $this->votoDAO->registrarVotoBrancoOuNulo($idFuncionario, $idEleicao, $_POST['tipoVoto']);
                } else {
                    $_SESSION['erro_voto'] = "Selecione um candidato ou voto branco/nulo.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                if ($resultado) {
                    unset($_SESSION['erro_voto']);
                    $_SESSION['sucesso_voto'] = "Voto registrado com sucesso!";
                    
                    // Adicionar informações do comprovante
                    $_SESSION['comprovante_voto'] = [
                        'id_eleicao' => $idEleicao,
                        'id_funcionario' => $idFuncionario,
                        'nome_funcionario' => $_SESSION['funcionario_logado']['nome_funcionario'] ?? 'N/A',
                        'cpf_funcionario' => $_SESSION['funcionario_logado']['cpf_funcionario'] ?? 'N/A',
                        'codigo_voto' => $_SESSION['funcionario_logado']['codigoVotoFuncionario'] ?? 'N/A',
                        'data_voto' => date('d/m/Y H:i:s'),
                        'tipo_voto' => isset($_POST['numeroCandidato']) ? 'Voto Registrado' : (isset($_POST['tipoVoto']) ? 'Voto ' . $_POST['tipoVoto'] : 'Voto Registrado')
                    ];
                    
                    header("Location: /code/cipa_t1/voto/sucesso");
                    exit;
                } else {
                    $_SESSION['erro_voto'] = "Erro ao registrar voto. Tente novamente.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }
            }
        }

        public function listarCandidatos($requisicao) {
            if ($requisicao === "GET") {
                // Buscar eleição ativa
                $idEleicao = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$idEleicao) {
                    $_SESSION['erro_voto'] = "Não há eleição ativa no momento.";
                    include "./views/voto/erro.php";
                    return;
                }

                $candidatos = $this->candidatoDAO->buscarPorEleicao($idEleicao);
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                
                $_SESSION['candidatos_lista'] = $candidatos;
                $_SESSION['eleicao_lista'] = $eleicao;

                include "./views/voto/listar_candidatos.php";
            }
        }

        public function sucesso() {
            include "./views/voto/sucesso.php";
        }
    }
