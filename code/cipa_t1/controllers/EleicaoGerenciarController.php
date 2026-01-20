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
                // Buscar eleição ativa completa com status de votação
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAtivaComStatusVotacao();
                
                if($eleicaoAtiva) {
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
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAtivaComStatusVotacao();
                
                if(!$eleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa para estender.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Atualizar data de término
                $resultado = $this->eleicaoDAO->atualizarDataFim($eleicaoAtiva['id_eleicao'], $novaDataFim);
                
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
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAtivaComStatusVotacao();
                
                if(!$eleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa para finalizar.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Finalizar eleição e atualizar data de término para o dia atual
                $resultado = $this->eleicaoDAO->atualizarStatusEDataFim($eleicaoAtiva['id_eleicao'], 'FINALIZADA');
                
                if($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Eleição finalizada com sucesso! Redirecionando para gerar a ata...";
                    // Redirecionar para gerar a ata automaticamente
                    header("Location: /code/cipa_t1/ata/gerar?eleicao=" . $eleicaoAtiva['id_eleicao']);
                    exit;
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao finalizar eleição.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }
            }
        }

        public function autorizarVotacao($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                // Buscar eleição ativa
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAtivaComStatusVotacao();
                
                if(!$eleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Autorizar votação
                $resultado = $this->eleicaoDAO->autorizarVotacao($eleicaoAtiva['id_eleicao']);
                
                if($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Votação autorizada com sucesso! O período de candidaturas foi encerrado e os funcionários agora podem votar.";
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao autorizar votação.";
                }

                header("Location: /code/cipa_t1/eleicao/gerenciar");
                exit;
            }
        }

        public function bloquearVotacao($requisicao) {
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }

            if($requisicao == "POST") {
                // Buscar eleição ativa
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAtivaComStatusVotacao();
                
                if(!$eleicaoAtiva) {
                    $_SESSION['erro_eleicao'] = "Não há eleição ativa.";
                    header("Location: /code/cipa_t1/eleicao/gerenciar");
                    exit;
                }

                // Bloquear votação
                $resultado = $this->eleicaoDAO->bloquearVotacao($eleicaoAtiva['id_eleicao']);
                
                if($resultado) {
                    $_SESSION['sucesso_eleicao'] = "Votação bloqueada com sucesso! O período de candidaturas foi reaberto e a votação foi desabilitada.";
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao bloquear votação.";
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
