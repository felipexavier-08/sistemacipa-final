<?php
require_once __DIR__ . "/../models/Documento.php";
require_once __DIR__ . "/../utils/Conexao.php";

class DocumentoDAO extends Conexao {
    private $db;
    
    public function __construct() {
        $this->db = self::pegarConexao();
    }

    public function inserir(Documento $model) {
        try {
            if (!$this->db) {
                error_log("Erro: Conexão com banco de dados não estabelecida");
                return false;
            }

            $sql = "INSERT INTO documento (            
                data_inicio_documento, 
                data_fim_documento, 
                pdf_documento, 
                titulo_documento, 
                tipo_documento
            ) 
            VALUES (            
                :dataInicio, 
                :dataFim, 
                :pdf, 
                :titulo, 
                :tipo
            )";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar SQL: " . implode(", ", $this->db->errorInfo()));
                return false;
            }
            
            $stmt->bindValue(':dataInicio', $model->getDataInicioDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':dataFim', $model->getDataFimDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':pdf', $model->getPdfDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':titulo', $model->getTituloDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $model->getTipoDocumento(), PDO::PARAM_STR);
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                error_log("Erro ao executar SQL: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro DocumentoDAO inserir: " . $e->getMessage());
            return false;
        }
    }

    public function buscarTodos() {
        try {
            $sql = "SELECT * FROM documento";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log("Erro DocumentoDAO buscarTodos: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId(int $id) {
        try {
            $sql = "SELECT * FROM documento WHERE id_documento = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_STR);
            $stmt->execute();
            $resposta = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function estaVinculadoAEleicao(int $idDocumento) {
        try {
            // Verificar se está vinculado a alguma eleição ATIVA
            $sql = "SELECT COUNT(*) FROM eleicao 
                    WHERE documento_fk = :id_documento 
                    AND status_eleicao = 'ABERTA'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id_documento", $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar vinculo de documento: " . $e->getMessage());
            return false;
        }
    }

    public function deletar(int $id) {
        try {
            $sql = "DELETE FROM documento WHERE id_documento = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar documento: " . $e->getMessage());
            return false;
        }
    }
}