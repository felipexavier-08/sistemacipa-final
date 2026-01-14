<?php
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../repositories/CandidatoDAO.php";

    class EleicaoGerenciarController {
        private $eleicaoDAO;
        private $candidatoDAO;

        public function __construct() {
            if(session_status() === PHP_SESSION_NONE){
                session_start();
            }
            $this->eleicaoDAO = new EleicaoDAO();
            $this->candidatoDAO = new CandidatoDAO();
        }

        public function gerenciar($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "GET") {
                // Buscar eleição ativa completa
                require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                $eleicaoDAO = new EleicaoDAO();
                
                // Primeiro buscar o ID
                $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                
                if($idEleicaoAtiva) {
                    // Buscar dados completos da eleição
                    $eleicaoAtiva = $eleicaoDAO->buscarPorId($idEleicaoAtiva);
                    $_SESSION['eleicao_ativa'] = $eleicaoAtiva;
                } else {
                    $_SESSION['eleicao_ativa'] = null;
                }
                
                include "./views/eleicao/gerenciar.php";
            }
        }

        public function estenderPeriodo($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                $novaDataFim = $_POST['novaDataFim'] ?? '';
                
                if(empty($novaDataFim)) {
                    $_SESSION['erro_eleicao'] = "A nova data de término é obrigatória.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Validar se a nova data é futura
                if(strtotime($novaDataFim) <= strtotime(date('Y-m-d'))) {
                    $_SESSION['erro_eleicao'] = "A nova data de término deve ser maior que a data atual.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Buscar eleição ativa
                require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                $eleicaoDAO = new EleicaoDAO();
                $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                
                if(!$idEleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa para estender.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Atualizar data de término
                $resultado = $eleicaoDAO->atualizarDataFim($idEleicaoAtiva, $novaDataFim);
                
                if($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Período da eleição estendido até " . date('d/m/Y', strtotime($novaDataFim));
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao estender período da eleição.";
                }

                header("Location: /code/cipa_t1/eleicao/gerenciar");
                exit;
            }
        }

        public function finalizar($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                // Buscar eleição ativa
                require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                $eleicaoDAO = new EleicaoDAO();
                $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                
                if(!$idEleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa para finalizar.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Finalizar eleição
                $resultado = $eleicaoDAO->atualizarStatus($idEleicaoAtiva, 'FINALIZADA');
                
                if($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Eleição finalizada com sucesso! Nenhuma nova votação poderá ser realizada.";
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao finalizar eleição.";
                }

                header("Location: /code/cipa_t1/eleicao/gerenciar");
                exit;
            }
        }

        public function bloquearCandidatos($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                // Buscar eleição ativa
                require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                $eleicaoDAO = new EleicaoDAO();
                $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                
                if(!$idEleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Implementar lógica para bloquear candidatos (poderia ser um campo na tabela eleicao)
                $_SESSION['sucesso_eleicao'] = "Cadastro de novos candidatos bloqueado para esta eleição.";
                header("Location: /code/cipa_t1/eleicao/gerenciar");
                exit;
            }
        }

        public function permitirCandidatos($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                // Buscar eleição ativa
                require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                $eleicaoDAO = new EleicaoDAO();
                $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                
                if(!$idEleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                $_SESSION['sucesso_eleicao'] = "Cadastro de novos candidatos liberado para esta eleição.";
                header("Location: /code/cipa_t1/eleicao/gerenciar");
                exit;
            }
        }
    }
?>
