<?php
declare(strict_types=1);

namespace App\Model;

use PDOException;

class UsuarioModel extends Model
{
    public function buscarTodos(): array
    {
        try {
            return $this->projetarTodos(
                "SELECT id, nome, email, usuario_nivel, dt_registro FROM usuarios ORDER BY nome ASC"
            ) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar todos os usuários: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            return $this->projetarEspecifico(
                "SELECT id, nome, email, usuario_nivel, dt_registro FROM usuarios WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?? null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário com ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar(array $dados): bool
    {
        try {
            error_log("Iniciando cadastro de usuário: " . json_encode($dados));

            $usuarioExistente = $this->projetarEspecifico(
                "SELECT id FROM usuarios WHERE email = :email LIMIT 1",
                ['email' => $dados['email']]
            );

            if ($usuarioExistente) {
                error_log("E-mail já cadastrado: " . $dados['email']);
                return false;
            }

            if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                error_log("Dados obrigatórios faltando: " . json_encode($dados));
                return false;
            }

            $dados['usuario_nivel'] = $dados['usuario_nivel'] ?? 'cliente';

            if (!str_starts_with($dados['senha'], '$2y$')) {
                $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }

            $resultado = $this->implementar(
                "INSERT INTO usuarios (nome, email, senha, usuario_nivel, dt_registro)
                 VALUES (:nome, :email, :senha, :usuario_nivel, NOW())",
                [
                    'nome' => $dados['nome'],
                    'email' => $dados['email'],
                    'senha' => $dados['senha'],
                    'usuario_nivel' => $dados['usuario_nivel']
                ]
            );

            error_log("Cadastro de usuário " . ($resultado ? "concluído" : "falhou"));
            return $resultado;

        } catch (PDOException $e) {
            error_log("Erro PDO ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(array $dados): bool
    {
        try {
            error_log("Iniciando atualização de usuário: " . json_encode($dados));

            $params = [
                'id' => $dados['id'],
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'usuario_nivel' => $dados['usuario_nivel'] ?? 'cliente'
            ];

            if (!empty($dados['senha'])) {
                $params['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
                $query = "UPDATE usuarios SET nome = :nome, email = :email, usuario_nivel = :usuario_nivel, senha = :senha WHERE id = :id";
            } else {
                $query = "UPDATE usuarios SET nome = :nome, email = :email, usuario_nivel = :usuario_nivel WHERE id = :id";
            }

            $this->implementar($query, $params);

            error_log("Atualização de usuário executada.");
            return true;

        } catch (PDOException $e) {
            error_log("Erro PDO ao atualizar usuário com ID {$dados['id']}: " . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        try {
            error_log("Iniciando exclusão de usuário ID: {$id}");

            $resultado = $this->implementar(
                "DELETE FROM usuarios WHERE id = :id",
                ['id' => $id]
            );

            error_log("Exclusão de usuário " . ($resultado ? "concluída" : "falhou"));
            return $resultado;

        } catch (PDOException $e) {
            error_log("Erro PDO ao excluir usuário ID {$id}: " . $e->getMessage());
            return false;
        }
    }
}
