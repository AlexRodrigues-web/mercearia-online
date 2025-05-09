<?php

namespace App\Model;

use PDOException;

class Funcionario extends Model
{
    /**
     * Lista todos os funcionários com informações de cargo e nível.
     */
    public function listar(): array
    {
        try {
            return parent::projetarTodos(
                "SELECT f.id, f.nome, f.ativo, c.nome AS cargo, n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 ORDER BY f.nome"
            );
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
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
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
        if (!$this->validarCSRF($tokenCSRF)) {
            $this->definirMensagemErro("Erro de segurança. Recarregue a página.");
            exit();
        }

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
                $this->definirMensagemErro("Erro ao cadastrar funcionário.");
            }
        }
    }

    /**
     * Atualiza os dados de um funcionário.
     */
    public function atualizar(array $dados, string $tokenCSRF): void
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            $this->definirMensagemErro("Erro de segurança. Recarregue a página.");
            exit();
        }

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
                $this->definirMensagemErro("Erro ao atualizar funcionário.");
            }
        }
    }

    /**
     * Exclui um funcionário.
     */
    public function excluir(int $id, string $tokenCSRF): void
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            $this->definirMensagemErro("Erro de segurança. Recarregue a página.");
            exit();
        }

        try {
            parent::implementar("DELETE FROM funcionario WHERE id = :id", ['id' => $id]);
            $this->definirMensagemSucesso("Funcionário excluído com sucesso!");
        } catch (PDOException $e) {
            error_log("Erro ao excluir funcionário: " . $e->getMessage());
            $this->definirMensagemErro("Erro ao excluir funcionário.");
        }
    }

    /**
     * Valida os dados do formulário.
     */
    private function validarDados(array &$dados, bool $atualizacao = false): bool
    {
        if (empty(trim($dados['nome'])) || strlen($dados['nome']) > 70) {
            $this->definirMensagemErro("Nome inválido.");
            return false;
        }

        if (!$atualizacao && (empty($dados['senha']) || strlen(trim($dados['senha'])) < 8)) {
            $this->definirMensagemErro("Senha deve ter pelo menos 8 caracteres.");
            return false;
        }

        if (!isset($dados['cargo_id'], $dados['nivel_id']) || !is_numeric($dados['cargo_id']) || !is_numeric($dados['nivel_id'])) {
            $this->definirMensagemErro("Cargo ou Nível inválidos.");
            return false;
        }

        return true;
    }

    private function validarCSRF(string $tokenCSRF): bool
    {
        return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $tokenCSRF);
    }
}
