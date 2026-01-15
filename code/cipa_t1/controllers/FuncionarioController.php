<?php

    require_once __DIR__ . "/../models/Funcionario.php";
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";
    require_once __DIR__ . "/../utils/Util.php";

    class FuncionarioController {

        private $dao;

        public function __construct() {


            if(session_status() === PHP_SESSION_NONE){
                session_start();
            }
            $this->dao = new FuncionarioDAO();
        }

        public function criarFuncionario($requisicao) {
            if($requisicao == "POST"){
                // Validar dados obrigatórios (incluindo código de voto)
                if (empty($_POST['nomeFuncionario']) || empty($_POST['sobrenomeFuncionario']) || 
                    empty($_POST['cpfFuncionario']) || empty($_POST['dataNascimentoFuncionario']) || 
                    empty($_POST['dataContratacaoFuncionario']) || empty($_POST['emailFuncionario']) || 
                    empty($_POST['senhaFuncionario']) || empty($_POST['codigoVotoFuncionario'])) {
                    $_SESSION['erro_funcionario'] = "Todos os campos obrigatórios devem ser preenchidos, incluindo o código de voto.";
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar CPF (11 dígitos)
                if (!preg_match('/^[0-9]{11}$/', $_POST['cpfFuncionario'])) {
                    $_SESSION['erro_funcionario'] = "CPF inválido. Digite apenas 11 números.";
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar telefone se preenchido
                if (!empty($_POST['telefoneFuncionario']) && !preg_match('/^[0-9]{11}$/', $_POST['telefoneFuncionario'])) {
                    $_SESSION['erro_funcionario'] = "Telefone inválido. Digite apenas 11 números.";
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar email
                if (!filter_var($_POST['emailFuncionario'], FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['erro_funcionario'] = "E-mail inválido.";
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                $func = new Funcionario(
                    $_POST['nomeFuncionario'],
                    $_POST['sobrenomeFuncionario'],
                    $_POST['cpfFuncionario'],
                    $_POST['dataNascimentoFuncionario'],
                    $_POST['dataContratacaoFuncionario'],
                    $_POST['telefoneFuncionario'],
                    $_POST['matriculaFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    $_POST['senhaFuncionario'],
                    $_POST['codigoVotoFuncionario'], // Código de voto do formulário
                    0
                );

                // Debug logs
                error_log("DEBUG: Tentando cadastrar funcionário");
                error_log("DEBUG: Código de voto: " . $_POST['codigoVotoFuncionario']);
                error_log("DEBUG: Dados - Nome: " . $_POST['nomeFuncionario'] . ", Email: " . $_POST['emailFuncionario'] . ", CPF: " . $_POST['cpfFuncionario']);

                $respostaBD = $this->dao->inserir($func);
                error_log("DEBUG: Resultado do inserir: " . ($respostaBD ? 'SUCESSO' : 'FALHA'));
                
                if($respostaBD) {
                    $_SESSION['sucesso_funcionario'] = "Funcionário cadastrado com sucesso! Código de voto: " . $_POST['codigoVotoFuncionario'];
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    $_SESSION['erro_funcionario'] = "Erro ao cadastrar funcionário. Verifique os dados e tente novamente.";
                    error_log("DEBUG: Redirecionando para formulário com erro");
                    include "./views/funcionario/cadastrar.php";
                }

            }

            if($requisicao == "GET"){            
                
                include "./views/funcionario/cadastrar.php";
            }
        }

        public function buscarFuncionarioPorCpf($requisicao) {
            if($requisicao == "GET") {
                $cpf = $_GET['cpf'] ?? '';
                
                if (empty($cpf)) {
                    $_SESSION['erro_funcionario'] = "CPF não informado para pesquisa.";
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
                
                // Validar CPF (11 dígitos)
                if (!preg_match('/^[0-9]{11}$/', $cpf)) {
                    $_SESSION['erro_funcionario'] = "CPF inválido. Digite apenas 11 números.";
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
                
                // Buscar funcionário por CPF
                $funcionarioData = $this->dao->buscarPorCpf($cpf);
                
                if($funcionarioData) {
                    $funcionarios = Util::converterArrayFuncionario([$funcionarioData]);
                    $_SESSION['funcionarios'] = $funcionarios;
                    $_SESSION['cpf_pesquisado'] = $cpf; // Guardar CPF pesquisado
                    $_SESSION['sucesso_funcionario'] = "Funcionário encontrado com o CPF: " . preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf);
                } else {
                    $_SESSION['funcionarios'] = [];
                    $_SESSION['erro_funcionario'] = "Nenhum funcionário encontrado com o CPF informado.";
                    $_SESSION['cpf_pesquisado'] = null;
                }
                
                header("Location: /code/cipa_t1/funcionario/listar");
                exit;
            }
        }

        public function buscarTodosFuncionarios($requisicao) {
            if($requisicao == "GET") {
                // Limpar CPF pesquisado ao listar todos
                $_SESSION['cpf_pesquisado'] = null;
                
                $funcionarios = $this->dao->buscarTodos();

                if(!empty($funcionarios)) {
                    $funcionarios = Util::converterArrayFuncionario($funcionarios);
                    
                    // Verificar se há eleição ativa e status de voto
                    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                    $eleicaoDAO = new EleicaoDAO();
                    $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                    
                    error_log("DEBUG: Eleição ativa encontrada: " . ($idEleicaoAtiva ? 'SIM' : 'NÃO'));
                    if($idEleicaoAtiva) {
                        error_log("DEBUG: ID Eleição Ativa: $idEleicaoAtiva");
                    }
                    
                    if($idEleicaoAtiva) {
                        require_once __DIR__ . "/../repositories/VotoDAO.php";
                        $votoDAO = new VotoDAO();
                        $idEleicao = (int)$idEleicaoAtiva;
                        
                        error_log("DEBUG: Verificando status de voto para " . count($funcionarios) . " funcionários");
                        
                        // Marcar status de voto para cada funcionário
                        foreach($funcionarios as $funcionario) {
                            $idFuncionario = $funcionario->getIdFuncionario();
                            $jaVotou = $votoDAO->funcionarioJaVotou($idFuncionario, $idEleicao);
                            $funcionario->setJaVotou($jaVotou);
                            error_log("DEBUG: Funcionário $idFuncionario - Status: " . ($jaVotou ? 'JÁ VOTOU' : 'NÃO VOTOU'));
                        }
                    } else {
                        error_log("DEBUG: Não há eleição ativa, marcando todos como não votaram");
                        // Se não há eleição ativa, marcar todos como não votaram
                        foreach($funcionarios as $funcionario) {
                            $funcionario->setJaVotou(false);
                        }
                    }
                    
                    $_SESSION['funcionarios'] = $funcionarios;
                    include "./views/funcionario/lista.php";
                }
                else{
                    $_SESSION['funcionarios'] = [];
                    include "./views/funcionario/lista.php";
                }
            }
        }              

        public function deletarFuncionario($requisicao) {
            if ($requisicao == "GET") {
                $respostaBD = $this->dao->deletar($_GET['id']);
                if ($respostaBD) {
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                } else {
                    echo "Erro ao deletar funcionário.";
                }
            }
        }


        public function cadastrarPorMatricula($requisicao) {
            if ($requisicao == "GET") {
                include "./views/funcionario/cadastrar_por_matricula.php";
            }

            if ($requisicao == "POST") {
                $funcionarioData = null;
                $acao = $_POST['acao'] ?? '';
                
                // Buscar por CPF apenas
                if ($acao === 'buscar_cpf' && !empty($_POST['cpfFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorCpf($_POST['cpfFuncionario']);
                }
                // Buscar por Matrícula apenas
                elseif ($acao === 'buscar_matricula' && !empty($_POST['matriculaFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorMatricula($_POST['matriculaFuncionario']);
                }
                // Buscar por ambos (comportamento original)
                elseif ($acao === 'buscar_ambos' && !empty($_POST['cpfFuncionario']) && !empty($_POST['matriculaFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorMatriculaECpf(
                        $_POST['matriculaFuncionario'],
                        $_POST['cpfFuncionario']
                    );
                }
                // Se nenhum campo foi preenchido ou ação inválida
                else {
                    $_SESSION['erro_matricula'] = "Preencha pelo menos um dos campos e selecione uma ação de busca.";
                    include "./views/funcionario/cadastrar_por_matricula.php";
                    return;
                }

                if ($funcionarioData) {
                    $funcionario = Util::converterArrayFuncionario([$funcionarioData])[0];
                    $_SESSION['funcionario_editar'] = $funcionario;
                    header("Location: /code/cipa_t1/funcionario/editar?id=" . $funcionario->getIdFuncionario());
                    exit;
                } else {
                    $_SESSION['erro_matricula'] = "Funcionário não encontrado com os dados informados.";
                    include "./views/funcionario/cadastrar_por_matricula.php";
                }
            }
        }

        public function editarFuncionario($requisicao) {
            if ($requisicao == "POST") {
                $func = new Funcionario(
                    $_POST['nomeFuncionario'],
                    $_POST['sobrenomeFuncionario'],
                    $_POST['cpfFuncionario'],
                    $_POST['dataNascimentoFuncionario'],
                    $_POST['dataContratacaoFuncionario'],
                    $_POST['telefoneFuncionario'],
                    $_POST['matriculaFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    !empty($_POST['senhaFuncionario']) ? $_POST['senhaFuncionario'] : '',
                    $_POST['codigoVotoFuncionario'] ?? '', // Código de voto do formulário
                    $_POST['idFuncionario']
                    
                );
        
                $respostaBD = $this->dao->atualizar($func);
        
                if ($respostaBD) {
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                } else {
                    echo "Erro ao editar funcionário.";
                }
            }
        
            if ($requisicao == "GET") {
                // Buscar funcionário do banco de dados
                $funcionarioData = $this->dao->buscarPorId($_GET['id']);
                if ($funcionarioData) {
                    $funcionario = Util::converterArrayFuncionario([$funcionarioData])[0];
                    $_SESSION['funcionario_editar'] = $funcionario;
                }
                include "./views/funcionario/editar.php";
            }
        }

    }


?>