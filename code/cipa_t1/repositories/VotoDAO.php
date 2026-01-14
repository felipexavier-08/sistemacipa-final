<?php
require_once __DIR__ . "/../models/Voto.php";
require_once __DIR__ . "/../utils/Conexao.php";

class VotoDAO extends Conexao {
        private ?PDO $db;

        public function __construct() {
            $this->db = self::pegarConexao();
        }

        // INSERIR - Inserir novo voto
        public function inserir(Voto $model, int $idFuncionario) {
            try {
                $sql = "INSERT INTO voto (
                    data_hora_voto,
                    usuario_fk
                ) VALUES (
                    NOW(),
                    :usuario_fk
                )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao inserir voto: " . $e->getMessage());
                return false;
            }
        }

        public function funcionarioJaVotou(int $idFuncionario, int $idEleicao) {
            try {
                // Verificar se o funcionário já votou nesta eleição
                // Verificar se há voto recente do funcionário (últimas 24h) e se há candidatos da eleição
                $sql = "SELECT COUNT(*) FROM voto v
                        WHERE v.usuario_fk = :usuario_fk 
                        AND DATE(v.data_hora_voto) = CURDATE()
                        AND EXISTS (
                            SELECT 1 FROM candidato c 
                            WHERE c.eleicao_fk = :eleicao_fk
                        )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                error_log("Erro ao verificar se funcionário votou: " . $e->getMessage());
                // Em caso de erro, retornar false para permitir tentativa
                return false;
            }
        }

        // REGISTRAR VOTO - Registrar voto em candidato
        public function registrarVoto(int $idFuncionario, int $idCandidato) {
            try {
                $this->db->beginTransaction();
                
                // Registrar o voto
                $sqlVoto = "INSERT INTO voto (data_hora_voto, usuario_fk, id_candidato) VALUES (NOW(), :usuario_fk, :candidato_fk)";
                $stmtVoto = $this->db->prepare($sqlVoto);
                $stmtVoto->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmtVoto->bindValue(":candidato_fk", $idCandidato, PDO::PARAM_INT);
                $stmtVoto->execute();
                
                // Atualizar quantidade de votos do candidato
                $sqlCandidato = "UPDATE candidato 
                                SET quantidade_voto_candidato = quantidade_voto_candidato + 1 
                                WHERE id_candidato = :id_candidato";
                $stmtCandidato = $this->db->prepare($sqlCandidato);
                $stmtCandidato->bindValue(":id_candidato", $idCandidato, PDO::PARAM_INT);
                $stmtCandidato->execute();
                
                $this->db->commit();
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Erro ao registrar voto: " . $e->getMessage());
                error_log("SQL executado: " . $sqlVoto);
                error_log("Parâmetros: idFuncionario=$idFuncionario, idCandidato=$idCandidato");
                return false;
            }
        }

        // REGISTRAR VOTO BRANCO OU NULO - Registrar voto em branco ou nulo
        public function registrarVotoBrancoOuNulo(int $idFuncionario, int $idEleicao, string $tipo) {
            try {
                $this->db->beginTransaction();

                // Registrar o voto
                $sqlVoto = "INSERT INTO voto (data_hora_voto, usuario_fk) VALUES (NOW(), :usuario_fk)";
                $stmtVoto = $this->db->prepare($sqlVoto);
                $stmtVoto->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmtVoto->execute();

                // Verificar se já existe registro de branco/nulo para esta eleição
                $sqlCheck = "SELECT id_branco_nulo FROM branconulo WHERE eleicao_fk = :eleicao_fk";
                $stmtCheck = $this->db->prepare($sqlCheck);
                $stmtCheck->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtCheck->execute();
                $existe = $stmtCheck->fetchColumn();

                if ($existe) {
                    // Atualizar
                    $campo = $tipo === 'BRANCO' ? 'quantidade_branco' : 'quantidade_nulo';
                    $sqlUpdate = "UPDATE branconulo SET $campo = $campo + 1 WHERE eleicao_fk = :eleicao_fk";
                    $stmtUpdate = $this->db->prepare($sqlUpdate);
                    $stmtUpdate->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                    $stmtUpdate->execute();
                } else {
                    // Inserir
                    $quantidadeBranco = $tipo === 'BRANCO' ? 1 : 0;
                    $quantidadeNulo = $tipo === 'NULO' ? 1 : 0;
                    $sqlInsert = "INSERT INTO branconulo (quantidade_branco, quantidade_nulo, eleicao_fk) 
                                 VALUES (:branco, :nulo, :eleicao_fk)";
                    $stmtInsert = $this->db->prepare($sqlInsert);
                    $stmtInsert->bindValue(":branco", $quantidadeBranco, PDO::PARAM_INT);
                    $stmtInsert->bindValue(":nulo", $quantidadeNulo, PDO::PARAM_INT);
                    $stmtInsert->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                    $stmtInsert->execute();
                }

                $this->db->commit();
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Erro ao registrar voto branco/nulo: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR BRANCOS E NULOS - Contar votos brancos e nulos
        public function buscarBrancosENulos(int $idEleicao) {
            try {
                $sql = "SELECT quantidade_branco, quantidade_nulo 
                        FROM branconulo 
                        WHERE eleicao_fk = :eleicao_fk
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado ?: ['quantidade_branco' => 0, 'quantidade_nulo' => 0];
            } catch (PDOException $e) {
                error_log("Erro ao buscar brancos e nulos: " . $e->getMessage());
                return ['quantidade_branco' => 0, 'quantidade_nulo' => 0];
            }
        }

        // CONTAR TOTAL VOTOS - Contar todos os votos da eleição
        public function contarTotalVotos(int $idEleicao) {
            try {
                // Contar votos de candidatos
                $sqlCandidatos = "SELECT SUM(quantidade_voto_candidato) FROM candidato WHERE eleicao_fk = :eleicao_fk";
                $stmtCandidatos = $this->db->prepare($sqlCandidatos);
                $stmtCandidatos->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtCandidatos->execute();
                $votosCandidatos = $stmtCandidatos->fetchColumn() ?: 0;

                // Contar brancos e nulos
                $sqlBrancosNulos = "SELECT SUM(quantidade_branco + quantidade_nulo) FROM branconulo WHERE eleicao_fk = :eleicao_fk";
                $stmtBrancosNulos = $this->db->prepare($sqlBrancosNulos);
                $stmtBrancosNulos->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtBrancosNulos->execute();
                $votosBrancosNulos = $stmtBrancosNulos->fetchColumn() ?: 0;

                return intval($votosCandidatos) + intval($votosBrancosNulos);
            } catch (PDOException $e) {
                error_log("Erro ao contar total de votos: " . $e->getMessage());
                return 0;
            }
        }

        // CONTAR ELEITORES QUE VOTARAM - Contar funcionários que votaram
        public function contarEleitoresQueVotaram(int $idEleicao) {
            try {
                $sql = "SELECT COUNT(DISTINCT v.usuario_fk) 
                        FROM voto v
                        INNER JOIN candidato c ON c.eleicao_fk = :eleicao_fk
                        WHERE DATE(v.data_hora_voto) >= (
                            SELECT data_inicio_eleicao FROM eleicao WHERE id_eleicao = :eleicao_fk
                        )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchColumn() ?: 0;
            } catch (PDOException $e) {
                error_log("Erro ao contar eleitores que votaram: " . $e->getMessage());
                return 0;
            }
        }
    }
