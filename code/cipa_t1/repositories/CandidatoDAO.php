<?php
    require_once __DIR__ . "/../models/Candidato.php";
    require_once __DIR__ . "/../utils/Conexao.php";
    class CandidatoDAO extends Conexao {
        private ?PDO $db;
        public function __construct() {
            $this->db = $this::pegarConexao();
        }

        public function inserir(Candidato $model, int $idFuncionario, int $idEleicao) {
            try {
                $sql = "INSERT INTO candidato (
                        foto_candidato,
                        numero_candidato,
                        cargo_candidato,
                        data_registro_candidato,
                        status_candidato,
                        quantidade_voto_candidato,
                        usuario_fk,
                        eleicao_fk
                    ) VALUES (
                        :foto,
                        :numero,
                        :cargo,
                        CURDATE(),
                        :status,
                        0,
                        :usuario_fk,
                        :eleicao_fk
                    )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":foto", $model->getFotoCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":numero", $model->getNumeroCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":cargo", $model->getCargoCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":status", $model->getStatusCandidatoAta(), PDO::PARAM_STR);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao inserir candidato: " . $e->getMessage());
                return false;
            }
        }

        public function buscarPorEleicao(int $idEleicao) {
            try {
                $sql = "SELECT c.*, f.nome_funcionario, f.sobrenome_funcionario 
                        FROM candidato c
                        INNER JOIN funcionario f ON c.usuario_fk = f.id_funcionario
                        WHERE c.eleicao_fk = :eleicao_fk
                        ORDER BY c.numero_candidato";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidatos por eleição: " . $e->getMessage());
                return [];
            }
        }

        public function buscarPorNumero(int $idEleicao, string $numeroCandidato) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND numero_candidato = :numero
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":numero", $numeroCandidato, PDO::PARAM_STR);
                $stmt->execute();
                
                return $stmt->fetchColumn() ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por número: " . $e->getMessage());
                return null;
            }
        }

        public function buscarPorNumeroEEleicao(string $numeroCandidato, int $idEleicao) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND numero_candidato = :numero
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":numero", $numeroCandidato, PDO::PARAM_STR);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por número e eleição: " . $e->getMessage());
                return false;
            }
        }

        public function buscarPorFuncionarioEEleicao(int $idFuncionario, int $idEleicao) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND usuario_fk = :usuario_fk
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por funcionário e eleição: " . $e->getMessage());
                return false;
            }
        }

        public function buscarResultadosPorEleicao(int $idEleicao) {
            try {
                $sql = "SELECT c.*, f.nome_funcionario, f.sobrenome_funcionario 
                        FROM candidato c
                        INNER JOIN funcionario f ON c.usuario_fk = f.id_funcionario
                        WHERE c.eleicao_fk = :eleicao_fk
                        ORDER BY c.quantidade_voto_candidato DESC, c.numero_candidato";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar resultados da eleição: " . $e->getMessage());
                return [];
            }
        }
    }