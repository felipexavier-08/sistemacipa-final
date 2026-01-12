<?php
    require_once __DIR__ . "/../models/Eleicao.php";
    require_once __DIR__ . "/../utils/Conexao.php";
    
    class EleicaoDAO extends Conexao {
        private ?PDO $db;
        public function __construct() {
            $this->db = $this::pegarConexao();
        }

        public function inserir(Eleicao $model) {
            try {
                $sql = "INSERT INTO eleicao (
                    data_inicio_eleicao,
                    data_fim_eleicao,
                    status_eleicao,
                    documento_fk
                ) VALUES (
                    :data_inicio,
                    :data_fim,
                    :status_e,
                    :doc
                )";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":data_inicio", $model->getDataInicioEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":data_fim", $model->getDataFimEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":status_e", $model->getStatusEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":doc", $model->getEditalFK()->getIdDocumento(), PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        public function buscarEleicaoAberta() {
            try {
                $sql = "SELECT id_eleicao FROM eleicao WHERE status_eleicao = 'ABERTA' LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                return $stmt->fetchColumn() ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleição aberta: " . $e->getMessage());
                return null;
            }
        }

        public function buscarTodas() {
            try {
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        ORDER BY e.data_inicio_eleicao DESC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleições: " . $e->getMessage());
                return [];
            }
        }

        public function buscarPorId(int $id) {
            try {
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.id_eleicao = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleição por ID: " . $e->getMessage());
                return null;
            }
        }

        public function eleicaoTerminou(int $idEleicao) {
            try {
                $sql = "SELECT data_fim_eleicao FROM eleicao WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $dataFim = $stmt->fetchColumn();
                if (!$dataFim) {
                    return false;
                }
                
                // Comparar data fim com data atual
                $hoje = date('Y-m-d');
                return $dataFim < $hoje;
            } catch (PDOException $e) {
                error_log("Erro ao verificar se eleição terminou: " . $e->getMessage());
                return false;
            }
        }

        public function buscarEleicoesFinalizadas() {
            try {
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.data_fim_eleicao < CURDATE()
                        ORDER BY e.data_fim_eleicao DESC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleições finalizadas: " . $e->getMessage());
                return [];
            }
        }
    }