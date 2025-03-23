<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;

class LoginModel extends Model
{
    /**
     * Busca o usuário para login (Funcionário ou Cliente).
     *
     * @param string $credencial Credencial do usuário fornecida pelo formulário.
     * @return array|false Retorna os dados do usuário autenticado ou false em caso de falha.
     */
    public function buscarUsuario(string $credencial): array|false
    {
        $credencial = trim($credencial);
        if (empty($credencial)) {
            error_log("Erro ao validar login: credencial vazia.");
            return false;
        }

        try {
            // 🟢 1️⃣ Primeiro, tenta encontrar na tabela `usuarios` (Clientes)
            $query = "SELECT id, nome, email, senha, 'cliente' AS nivel, 'cliente' AS tipo 
                      FROM usuarios 
                      WHERE email = :credencial 
                      LIMIT 1";

            $usuarioDados = $this->projetarEspecifico($query, ['credencial' => $credencial]);

            if (!$usuarioDados) {
                // 🟠 2️⃣ Se não encontrou em `usuarios`, tenta encontrar em `funcionario`
                $query = "SELECT f.id, f.nome, f.credencial AS email, f.senha, f.nivel_id, 'funcionario' AS tipo
                          FROM funcionario f 
                          WHERE f.credencial = :credencial 
                          LIMIT 1";

                $usuarioDados = $this->projetarEspecifico($query, ['credencial' => $credencial]);

                if ($usuarioDados) {
                    // 🔹 Converte `nivel_id` para 'admin' ou 'funcionario'
                    $usuarioDados['nivel'] = $this->mapearNivel($usuarioDados['nivel_id']);
                    unset($usuarioDados['nivel_id']); // Remove campo desnecessário
                }
            } else {
                // Se encontrou em `usuarios`, define como 'cliente'
                $usuarioDados['nivel'] = 'cliente';
            }

            return $usuarioDados ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
            return false;
        }
    }

    /**
     * Mapeia `nivel_id` da tabela `funcionario` para 'admin' ou 'funcionario'.
     *
     * @param int $nivelId ID do nível do funcionário.
     * @return string Retorna "admin" ou "funcionario".
     */
    private function mapearNivel(int $nivelId): string
    {
        return match ($nivelId) {
            1 => 'admin',
            2 => 'funcionario',
            default => 'funcionario',
        };
    }

    /**
     * Busca as permissões do usuário com base no nível de acesso.
     *
     * @param int $usuarioId ID do usuário.
     * @param string $tipo Tipo de usuário (funcionario ou cliente).
     * @return array Lista de permissões do usuário.
     */
    public function buscarPermissoes(int $usuarioId, string $tipo): array
    {
        try {
            if ($tipo === 'cliente') {
                // 🔹 Se for cliente, buscar permissões na `usuario_pg_privada`
                $permissoes = $this->projetarLista(
                    "SELECT p.nome FROM usuario_pg_privada up
                     JOIN pg_privada p ON up.pg_privada_id = p.id
                     WHERE up.usuario_id = :usuario_id",
                    ['usuario_id' => $usuarioId]
                );
            } else {
                // 🔹 Se for funcionário, buscar permissões na `funcionario_pg_privada`
                $permissoes = $this->projetarLista(
                    "SELECT p.nome FROM funcionario_pg_privada fp
                     JOIN pg_privada p ON fp.pg_privada_id = p.id
                     WHERE fp.funcionario_id = :usuario_id",
                    ['usuario_id' => $usuarioId]
                );
            }

            // ✅ Se não houver permissões, atribuir permissões mínimas
            return !empty($permissoes) ? array_column($permissoes, 'nome') : ["pagina_publica", "perfil"];
        } catch (PDOException $e) {
            error_log("Erro ao buscar permissões do usuário: " . $e->getMessage());
            return ["pagina_publica", "perfil"];
        }
    }

    /**
     * Busca o nível do funcionário pelo ID.
     *
     * @param int $funcionarioId ID do funcionário.
     * @return string Retorna "admin" ou "funcionario".
     */
    public function buscarNivelFuncionario(int $funcionarioId): string
    {
        try {
            $nivel = $this->projetarEspecifico(
                "SELECT n.nome FROM funcionario f 
                 JOIN nivel n ON f.nivel_id = n.id 
                 WHERE f.id = :id LIMIT 1",
                ['id' => $funcionarioId]
            );

            return $nivel['nome'] ?? 'funcionario';
        } catch (PDOException $e) {
            error_log("Erro ao buscar nível do funcionário: " . $e->getMessage());
            return 'funcionario'; // Caso haja erro, assume funcionário padrão
        }
    }
}
