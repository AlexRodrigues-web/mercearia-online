<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;
use Psr\Log\LoggerInterface;

class PaginaPublica extends Model
{
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return "{$protocol}://{$host}{$scriptDir}/";
    }

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome, dt_registro FROM pg_publica ORDER BY dt_registro DESC") ?: [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar páginas públicas", $e);
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
                "SELECT id, nome, dt_registro FROM pg_publica WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar página pública", $e);
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

            parent::implementar(
                "INSERT INTO pg_publica (nome, dt_registro) VALUES (:nome, :dt_registro)",
                $dados
            );

            return ['sucesso' => "Página pública cadastrada com sucesso!"];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao cadastrar página pública", $e);
            return ['erro' => "Erro ao cadastrar página pública."];
        }
    }

    public function excluir(int $id, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->editar($id)) {
            return ['erro' => "Página pública não encontrada."];
        }

        try {
            parent::implementar("DELETE FROM pg_publica WHERE id = :id", ['id' => $id]);
            return ['sucesso' => "Página pública excluída com sucesso!"];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir página pública", $e);
            return ['erro' => "Erro ao excluir página pública."];
        }
    }

    public function atualizar(array $dados, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->editar($dados['id'])) {
            return ['erro' => "Página pública não encontrada."];
        }

        if (!$this->validarDados($dados, true)) {
            return ['erro' => "Preencha todos os campos corretamente."];
        }

        try {
            parent::implementar(
                "UPDATE pg_publica SET nome = :nome WHERE id = :id",
                ['id' => $dados['id'], 'nome' => $dados['nome']]
            );

            return ['sucesso' => "Página pública atualizada com sucesso!"];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar página pública", $e);
            return ['erro' => "Erro ao atualizar página pública."];
        }
    }

    private function validarDados(array &$dados, bool $atualizacao = false): bool
    {
        if ($atualizacao) {
            if (!isset($dados['id']) || !filter_var($dados['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
                return false;
            }
        }

        if (empty(trim($dados['nome'])) || strlen(trim($dados['nome'])) < 3 || strlen(trim($dados['nome'])) > 50) {
            return false;
        }

        $dados['nome'] = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        return true;
    }

    private function registrarErro(string $mensagem, PDOException $e): void
    {
        $this->logger?->error("$mensagem: " . $e->getMessage());
    }

    private function validarCSRF(string $tokenCSRF): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenCSRF);
    }
}
