<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;
use Psr\Log\LoggerInterface;

class PaginaPrivadaModel extends Model
{
    private array $formObrigatorio = ['nome'];
    private int $formObrigatorioQuantidade = 1;
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome, dt_registro FROM pg_privada ORDER BY dt_registro DESC") ?: [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar páginas privadas: " . $e->getMessage());
            return [];
        }
    }

    public function editar(int $id): ?array
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            return null;
        }

        try {
            return parent::projetarEspecifico(
                "SELECT id, nome, dt_registro FROM pg_privada WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar a página privada: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar(array $dados, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->validarDados($dados)) {
            return ['erro' => "Preencha todos os campos corretamente."];
        }

        try {
            $dados['dt_registro'] = date('Y-m-d H:i:s');
            parent::implementar("INSERT INTO pg_privada (nome, dt_registro) VALUES (:nome, :dt_registro)", $dados);
            return ['sucesso' => "Página privada cadastrada com sucesso!"];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao cadastrar página privada: " . $e->getMessage());
            return ['erro' => "Erro ao cadastrar página privada."];
        }
    }

    public function excluir(int $id, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->editar($id)) {
            return ['erro' => "Página privada não encontrada."];
        }

        try {
            parent::implementar("DELETE FROM pg_privada WHERE id = :id", ['id' => $id]);
            return ['sucesso' => "Página privada excluída com sucesso!"];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir página privada: " . $e->getMessage());
            return ['erro' => "Erro ao excluir página privada."];
        }
    }

    public function atualizar(array $dados): bool
    {
        if (!$this->editar($dados['id'])) {
            return false;
        }

        if (!$this->validarDados($dados, true)) {
            return false;
        }

        try {
            parent::implementar(
                "UPDATE pg_privada SET nome = :nome WHERE id = :id",
                ['id' => $dados['id'], 'nome' => $dados['nome']]
            );
            return true;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar página privada: " . $e->getMessage());
            return false;
        }
    }

    private function validarDados(array &$dados, bool $atualizacao = false): bool
    {
        if ($atualizacao) {
            if (!isset($dados['id']) || !filter_var($dados['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
                return false;
            }
        }

        if (empty(trim($dados['nome'])) || strlen(trim($dados['nome'])) < 3) {
            return false;
        }

        $dados['nome'] = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        return true;
    }

    private function registrarErro(string $mensagem): void
    {
        if ($this->logger) {
            $this->logger->error($mensagem);
        } else {
            error_log($mensagem);
        }
    }

    private function validarCSRF(string $tokenCSRF): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenCSRF);
    }
}
