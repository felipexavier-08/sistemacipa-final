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
                $func = new Funcionario(
                    $_POST['nomeFuncionario'],
                    $_POST['sobrenomeFuncionario'],
                    $_POST['cpfFuncionario'],
                    $_POST['dataNascimentoFuncionario'],
                    $_POST['dataContratacaoFuncionario'],
                    $_POST['telefoneFuncionario'],
                    $_POST['matriculaFuncionario'],
                    $_POST['codigoVotoFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    $_POST['senhaFuncionario'],
                    0
                );

                $respostaBD = $this->dao->inserir($func);
                if($respostaBD) {
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    echo "Erro ao cadastrar funcionário.";
                }

            }

            if($requisicao == "GET"){            
                
                include "./views/funcionario/cadastrar.php";
            }

        }


        public function buscarTodosFuncionarios($requisicao) {
            if($requisicao == "GET") {
                $funcionarios = $this->dao->buscarTodos();

                if(!empty($funcionarios)) {
                    $funcionarios = Util::converterArrayFuncionario($funcionarios);                    
                    $_SESSION['funcionarios'] = $funcionarios;
                    include "./views/funcionario/lista.php";
                }
                else{
                    
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
                    $_POST['codigoVotoFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    !empty($_POST['senhaFuncionario']) ? $_POST['senhaFuncionario'] : '',
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