<?php

require_once __DIR__ . "/../models/Funcionario.php";
require_once __DIR__ . "/../utils/Conexao.php";

class FuncionarioDAO extends Conexao {

    private $db;

    public function __construct() {
        try {
            $this->db = $this::pegarConexao();
        } catch (PDOException $e) {
            // Log de erro ou redirecionamento
            die("Erro ao conectar: " . $e->getMessage());
        }
    }

    // CREATE
    public function inserir(Funcionario $model) {
        try {
            $sql = "INSERT INTO funcionario (
                nome_funcionario, 
                sobrenome_funcionario,
                cpf_funcionario, 
                data_nascimento_funcionario,
                data_contratacao_funcionario,
                telefone_funcionario, 
                matricula_funcionario,
                cod_voto_funcionario,
                ativo_funcionario,
                adm_funcionario, 
                email_funcionario,
                senha_funcionario
            ) VALUES (
                :nome, 
                :sobrenome, 
                :cpf, 
                :nasc, 
                :contra, 
                :tel, 
                :matri, 
                :voto, 
                :ativo, 
                :adm, 
                :email, 
                :senha
            )";
            
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':nome',      $model->getNomeFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':cpf',       $model->getCpfFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':tel',       $model->getTelefoneFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':matri',     $model->getMatriculaFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':ativo',     $model->getAtivoFuncionario() ? 1 : 0, PDO::PARAM_BOOL);
            $stmt->bindValue(':adm',       $model->getAdmFuncionario() ? 1 : 0, PDO::PARAM_BOOL);
            $stmt->bindValue(':email',     $model->getEmailFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':senha',     $model->getSenhaFuncionario(), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao inserir funcionário: " . $e->getMessage());
            return false;
        }
    }


    // READ - Buscar todos
    public function buscarTodos() {
        try {
            $sql = "SELECT * FROM funcionario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários: " . $e->getMessage());
            return [];
        }
    }

    // READ - Buscar por ID
    public function buscarPorId(int $id) {
        try {
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por ID: " . $e->getMessage());
            return null;
        }
    }

    // READ - Buscar por Matrícula e CPF
    public function buscarPorMatriculaECpf(string $matricula, string $cpf) {
        try {
            $sql = "SELECT * FROM funcionario WHERE matricula_funcionario = :matricula AND cpf_funcionario = :cpf LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por matrícula e CPF: " . $e->getMessage());
            return null;
        }
    }

    // UPDATE
    public function atualizar(Funcionario $model) {
        try {
            // Se a senha foi preenchida, atualiza também
            if (!empty($model->getSenhaFuncionario())) {
                $sql = "UPDATE funcionario 
                SET 
                    nome_funcionario = :nome,
                    sobrenome_funcionario = :sobrenome, 
                    cpf_funcionario = :cpf,
                    data_nascimento_funcionario = :nasc,
                    data_contratacao_funcionario = :contra,
                    telefone_funcionario = :tel,
                    matricula_funcionario = :matri,
                    cod_voto_funcionario = :voto,
                    ativo_funcionario = :ativo, 
                    adm_funcionario = :adm,
                    email_funcionario = :email,
                    senha_funcionario = :senha
                    WHERE id_funcionario = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':nome',      $model->getNomeFuncionario());
                $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario());
                $stmt->bindValue(':cpf',       $model->getCpfFuncionario());
                $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario());
                $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario());
                $stmt->bindValue(':tel',       $model->getTelefoneFuncionario());
                $stmt->bindValue(':matri',     $model->getMatriculaFuncionario());
                $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario());
                $stmt->bindValue(':ativo',     $model->getAtivoFuncionario(), PDO::PARAM_INT);
                $stmt->bindValue(':adm',       $model->getAdmFuncionario(),   PDO::PARAM_INT);
                $stmt->bindValue(':email',     $model->getEmailFuncionario());
                $stmt->bindValue(':senha',     $model->getSenhaFuncionario());
                $stmt->bindValue(':id',        $model->getIdFuncionario(),    PDO::PARAM_INT);
            } else {
                $sql = "UPDATE funcionario 
                SET 
                    nome_funcionario = :nome,
                    sobrenome_funcionario = :sobrenome, 
                    cpf_funcionario = :cpf,
                    data_nascimento_funcionario = :nasc,
                    data_contratacao_funcionario = :contra,
                    telefone_funcionario = :tel,
                    matricula_funcionario = :matri,
                    cod_voto_funcionario = :voto,
                    ativo_funcionario = :ativo, 
                    adm_funcionario = :adm,
                    email_funcionario = :email
                    WHERE id_funcionario = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':nome',      $model->getNomeFuncionario());
                $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario());
                $stmt->bindValue(':cpf',       $model->getCpfFuncionario());
                $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario());
                $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario());
                $stmt->bindValue(':tel',       $model->getTelefoneFuncionario());
                $stmt->bindValue(':matri',     $model->getMatriculaFuncionario());
                $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario());
                $stmt->bindValue(':ativo',     $model->getAtivoFuncionario(), PDO::PARAM_INT);
                $stmt->bindValue(':adm',       $model->getAdmFuncionario(),   PDO::PARAM_INT);
                $stmt->bindValue(':email',     $model->getEmailFuncionario());
                $stmt->bindValue(':id',        $model->getIdFuncionario(),    PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar funcionário: " . $e->getMessage());
            return false;
        }
    }

    // DELETE
    public function deletar(int $id) {
        try {
            $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar funcionário: " . $e->getMessage());
            return false;
        }
    }

    // Buscar por código de voto
    public function buscarPorCodigoVoto(string $codigoVoto) {
        try {
            $sql = "SELECT * FROM funcionario WHERE cod_voto_funcionario = :codigo AND ativo_funcionario = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':codigo', $codigoVoto, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por código de voto: " . $e->getMessage());
            return null;
        }
    }

    // Buscar por CPF
    public function buscarPorCpf(string $cpf) {
        try {
            // Remove formatação do CPF
            $cpf = preg_replace('/[^0-9]/', '', $cpf);
            
            $sql = "SELECT * FROM funcionario WHERE cpf_funcionario = :cpf LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por CPF: " . $e->getMessage());
            return null;
        }
    }
}