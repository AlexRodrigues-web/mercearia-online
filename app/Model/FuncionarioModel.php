<?php
declare(strict_types=1);

namespace App\Model;

use PDOException;

class FuncionarioModel extends Model
{
    public function listar(): array
    {
        try {
            return $this->projetarTodos(
                "SELECT 
                    f.id,
                    f.nome,
                    f.ativo,
                    c.nome AS cargo,
                    n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 ORDER BY f.nome"
            ) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao listar funcionários: " . $e->getMessage());
            return [];
        }
    }

    public function obterPorId(int $id): ?array
    {
        try {
            return $this->projetarEspecifico(
                "SELECT 
                    f.id,
                    f.nome,
                    f.ativo,
                    f.credencial,
                    f.cargo_id,
                    f.nivel_id,
                    c.nome AS cargo,
                    n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 WHERE f.id = :id
                 LIMIT 1",
                ['id' => $id]
            ) ?? null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar(array $dados): bool
    {
        try {
            if (empty($dados['nome']) || !isset($dados['cargo_id'], $dados['nivel_id'], $dados['credencial'], $dados['senha'])) {
                return false;
            }

            $params = [
                'nome'       => trim($dados['nome']),
                'ativo'      => !empty($dados['ativo']) ? 1 : 0,
                'cargo_id'   => (int) $dados['cargo_id'],
                'nivel_id'   => (int) $dados['nivel_id'],
                'credencial' => trim($dados['credencial']),
                'senha'      => $dados['senha'], // atenção... já está criptografada no controller
            ];

            return $this->implementar(
                "INSERT INTO funcionario (nome, ativo, cargo_id, nivel_id, credencial, senha, dt_registro)
                 VALUES (:nome, :ativo, :cargo_id, :nivel_id, :credencial, :senha, NOW())",
                $params
            );
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar funcionário: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(array $dados): bool
    {
        try {
            if (empty($dados['id']) || empty($dados['nome']) || !isset($dados['cargo_id'], $dados['nivel_id'])) {
                return false;
            }

            $params = [
                'id'         => (int) $dados['id'],
                'nome'       => trim($dados['nome']),
                'ativo'      => !empty($dados['ativo']) ? 1 : 0,
                'cargo_id'   => (int) $dados['cargo_id'],
                'nivel_id'   => (int) $dados['nivel_id'],
            ];

            $sql = "UPDATE funcionario
                    SET nome = :nome,
                        ativo = :ativo,
                        cargo_id = :cargo_id,
                        nivel_id = :nivel_id
                    WHERE id = :id";

            return $this->implementar($sql, $params);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar funcionário ID {$dados['id']}: " . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        try {
            return $this->implementar(
                "DELETE FROM funcionario WHERE id = :id",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            error_log("Erro ao excluir funcionário ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function credencialExiste(string $credencial): bool
    {
        try {
            $resultado = $this->projetarEspecifico(
                "SELECT id FROM funcionario WHERE credencial = :credencial LIMIT 1",
                ['credencial' => $credencial]
            );

            return !empty($resultado);
        } catch (PDOException $e) {
            error_log("Erro ao verificar credencial '{$credencial}': " . $e->getMessage());
            return false;
        }
    }
}
