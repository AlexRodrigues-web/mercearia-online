<?php

namespace App\Model;

use PDOException;

class Fornecedor extends Model
{
    /**
     * Lista todos os funcionários com informações de cargo e nível.
     */
    public function listar(): array
    {
        try {
            $funcionarios = parent::projetarTodos(
                "SELECT f.id, f.nome, f.ativo, c.nome AS cargo, n.nome AS nivel
                 FROM fornecedor f
                 -- INNER JOIN cargo c ON f.cargo_id = c.id
                 -- INNER JOIN nivel n ON f.nivel_id = n.id
                 ORDER BY f.nome"
            );

            if (!$funcionarios) {
                error_log("Nenhum funcionário encontrado.");
            }

            return $funcionarios ?: [];
        } catch (PDOException $e) {
            error_log("Erro ao listar funcionários: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém um funcionário específico para edição.
     */
    public function editar(int $id): array
    {
        try {
            return parent::projetarEspecifico(
                "SELECT f.id, f.nome, f.ativo, c.id AS cargo_id, n.id AS nivel_id
                 FROM fornecedor f
                 -- INNER JOIN cargo c ON f.cargo_id = c.id
                 -- INNER JOIN nivel n ON f.nivel_id = n.id
                 WHERE f.id = :id LIMIT 1",
                ['id' => $id],
                false
            ) ?: [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cadastra um novo funcionário.
     */
    public function cadastrar(array $dados, string $tokenCSRF): void
    {
        $this->validarCSRF($tokenCSRF);

        if ($this->validarDados($dados)) {
            try {
                $dados['senha'] = password_hash(trim($dados['senha']), PASSWORD_DEFAULT);
                $dados['dt_registro'] = date('Y-m-d H:i:s');

                parent::implementar(
                    "INSERT INTO funcionario (nome, ativo, cargo_id, nivel_id, credencial, senha, dt_registro)
                     VALUES (:nome, :ativo, :cargo_id, :nivel_id, :credencial, :senha, :dt_registro)",
                    $dados
                );

                $this->definirMensagemSucesso("Funcionário cadastrado com sucesso!");
            } catch (PDOException $e) {
                error_log("Erro ao cadastrar funcionário: " . $e->getMessage());
                throw new PDOException("Erro ao cadastrar funcionário.");
            }
        }
    }

    /**
     * Atualiza os dados de um funcionário.
     */
    public function atualizar(array $dados, string $tokenCSRF): void
    {
        $this->validarCSRF($tokenCSRF);

        if ($this->validarDados($dados, true)) {
            try {
                $sql = "UPDATE funcionario SET nome = :nome, ativo = :ativo, cargo_id = :cargo_id, nivel_id = :nivel_id";
                $params = [
                    'id' => $dados['id'],
                    'nome' => trim($dados['nome']),
                    'ativo' => $dados['ativo'],
                    'cargo_id' => $dados['cargo_id'],
                    'nivel_id' => $dados['nivel_id']
                ];

                if (!empty($dados['senha'])) {
                    $sql .= ", senha = :senha";
                    $params['senha'] = password_hash(trim($dados['senha']), PASSWORD_DEFAULT);
                }

                $sql .= " WHERE id = :id";
                parent::implementar($sql, $params);

                $this->definirMensagemSucesso("Funcionário atualizado com sucesso!");
            } catch (PDOException $e) {
                error_log("Erro ao atualizar funcionário: " . $e->getMessage());
                throw new PDOException("Erro ao atualizar funcionário.");
            }
        }
    }

    /**
     * Exclui um funcionário.
     */
    public function excluir(int $id, string $tokenCSRF): void
    {
        $this->validarCSRF($tokenCSRF);

        try {
            $funcionarioExiste = parent::projetarEspecifico(
                "SELECT id FROM fornecedor WHERE id = :id",
                ['id' => $id],
                false
            );

            if (!$funcionarioExiste) {
                throw new PDOException("Funcionário não encontrado.");
            }

            parent::implementar("DELETE FROM fornecedor WHERE id = :id", ['id' => $id]);
            $this->definirMensagemSucesso("Funcionário excluído com sucesso!");
        } catch (PDOException $e) {
            error_log("Erro ao excluir funcionário: " . $e->getMessage());
            throw new PDOException("Erro ao excluir funcionário.");
        }
    }

    /**
     * Valida os dados do formulário.
     */
    private function validarDados(array &$dados, bool $atualizacao = false): bool
    {
        if (empty(trim($dados['nome'])) || strlen($dados['nome']) > 70) {
            throw new PDOException("Nome inválido.");
        }

        if (!$atualizacao && (empty($dados['senha']) || strlen(trim($dados['senha'])) < 8)) {
            throw new PDOException("Senha deve ter pelo menos 8 caracteres.");
        }

        if (!isset($dados['cargo_id'], $dados['nivel_id']) || !is_numeric($dados['cargo_id']) || !is_numeric($dados['nivel_id'])) {
            throw new PDOException("Cargo ou Nível inválidos.");
        }

        $cargoExiste = parent::projetarEspecifico("SELECT id FROM cargo WHERE id = :id", ['id' => $dados['cargo_id']], false);
        $nivelExiste = parent::projetarEspecifico("SELECT id FROM nivel WHERE id = :id", ['id' => $dados['nivel_id']], false);

        if (!$cargoExiste || !$nivelExiste) {
            throw new PDOException("Cargo ou nível não encontrados.");
        }

        return true;
    }

    /**
     * Valida o CSRF Token antes de processar requisições.
     */
    private function validarCSRF(string $tokenCSRF): void
    {
        if (!isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $tokenCSRF)) {
            error_log("Tentativa de CSRF detectada.");
            throw new PDOException("Erro de segurança. Recarregue a página.");
        }
    }
}
