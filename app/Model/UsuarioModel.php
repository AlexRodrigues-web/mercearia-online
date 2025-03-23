<?php
declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;

class UsuarioModel extends Model
{
    /**
     * Retorna todos os usuários cadastrados.
     */
    public function buscarTodos(): array
    {
        try {
            return $this->projetarTodos(
                "SELECT id, nome, email, usuario_nivel FROM usuarios ORDER BY nome ASC"
            ) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna os dados de um usuário específico.
     */
    public function buscarPorId(int $id): ?array
    {
        try {
            return $this->projetarEspecifico(
                "SELECT id, nome, email, usuario_nivel FROM usuarios WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?? null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário com ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cadastra um novo usuário no sistema.
     */
    public function cadastrar(array $dados): bool
    {
        try {
            // Verifica se o e-mail já existe
            $usuarioExistente = $this->projetarEspecifico(
                "SELECT id FROM usuarios WHERE email = :email LIMIT 1",
                ['email' => $dados['email']]
            );

            if ($usuarioExistente) {
                $_SESSION['msg_erro'][] = "E-mail já cadastrado.";
                return false;
            }

            // Hash da senha
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

            return $this->implementar(
                "INSERT INTO usuarios (nome, email, senha, usuario_nivel) 
                 VALUES (:nome, :email, :senha, :usuario_nivel)",
                $dados
            );
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza os dados de um usuário existente.
     */
    public function atualizar(array $dados): bool
    {
        try {
            $params = [
                'id' => $dados['id'],
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'usuario_nivel' => $dados['usuario_nivel']
            ];

            if (!empty($dados['senha'])) {
                $params['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
                $query = "UPDATE usuarios SET nome = :nome, email = :email, usuario_nivel = :usuario_nivel, senha = :senha WHERE id = :id";
            } else {
                $query = "UPDATE usuarios SET nome = :nome, email = :email, usuario_nivel = :usuario_nivel WHERE id = :id";
            }

            return $this->implementar($query, $params);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário com ID {$dados['id']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um usuário do banco de dados.
     */
    public function excluir(int $id): bool
    {
        try {
            return $this->implementar(
                "DELETE FROM usuarios WHERE id = :id",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário com ID {$id}: " . $e->getMessage());
            return false;
        }
    }
}
