<?php
    require_once __DIR__ . "/../models/Documento.php";
    require_once __DIR__ . "/../models/Funcionario.php";
    abstract class Util {
        public static function converterArrayDoc($arrayAssociativo) {
            $documentos = [];
            foreach ($arrayAssociativo as $item) {
                $documento = new Documento(
                    $item['data_inicio_documento'],  // Obrigatório
                    $item['data_fim_documento'],     // Obrigatório
                    $item['titulo_documento'],       // Obrigatório
                    $item['tipo_documento'],         // Obrigatório
                    $item['pdf_documento'],          // Opcional (valor padrão "")
                    $item['data_hora_documento'],    // Opcional (valor padrão "")
                    $item['id_documento']            // Opcional (valor padrão 0)
                );
                
                $documentos[] = $documento;
            }

            return $documentos;
        }

        public static function converterArrayFuncionario($arrayAssociativo) {
            $funcionarios = [];
            foreach ($arrayAssociativo as $item) {
                $funcionario = new Funcionario(
                    $item['nome_funcionario'],
                    $item['sobrenome_funcionario'],
                    $item['cpf_funcionario'],
                    $item['data_nascimento_funcionario'],
                    $item['data_contratacao_funcionario'],
                    $item['telefone_funcionario'],
                    $item['matricula_funcionario'],
                    $item['cod_voto_funcionario'],
                    $item['ativo_funcionario'],
                    $item['adm_funcionario'],
                    $item['email_funcionario'],
                    $item['senha_funcionario'],
                    $item['id_funcionario']
                    );
                    $funcionarios[] = $funcionario;
            }
            return $funcionarios;
        }
    }