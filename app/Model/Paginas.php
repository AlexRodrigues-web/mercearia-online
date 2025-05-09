<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;
use Psr\Log\LoggerInterface;

class PaginaPublica extends Model
{
    private array $formObrigatorio = [];
    private array $formValido = [];
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    /**
     * Lista todas as páginas públicas cadastradas.
     */
    public function listar(): array
    {
        try {
            $resultados = parent::projetarTodos("SELECT id, nome, dt_registro FROM pg_publica ORDER BY dt_registro DESC");
            return is_array($resultados) ? $resultados : [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar páginas públicas", $e);
            return [];
        }
    }

    /**
     * Busca os detalhes de uma página pública específica.
     */
    public function editar(int $id): ?array
    {
        if (!parent::valida_int($id, 'id', '*ID inválido', 1)) {
            return null;
        }

        try {
            $pagina = parent::projetarEspecifico(
                "SELECT id, nome, dt_registro FROM pg_publica WHERE id = :id LIMIT 1",
                ['id' => $id]
            );

            return is_array($pagina) ? $pagina : null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar página pública", $e);
            return null;
        }
    }

    /**
     * Cadastra uma nova página pública.
     */
    public function cadastrar(array $dados, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->validarFormulario($dados, ['nome'])) {
            return ['erro' => "Preencha todos os campos obrigatórios."];
        }

        // Sanitização e validação aprimorada
        $dados['nome'] = trim($dados['nome']);
        if (strlen($dados['nome']) < 3 || strlen($dados['nome']) > 50) {
            return ['erro' => "O nome deve ter entre 3 e 50 caracteres."];
        }
        $dados['nome'] = htmlspecialchars($dados['nome'], ENT_QUOTES, 'UTF-8');

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

    /**
     * Exclui uma página pública.
     */
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

    /**
     * Atualiza os dados de uma página pública.
     */
    public function atualizar(array $dados, string $tokenCSRF): array
    {
        if (!$this->validarCSRF($tokenCSRF)) {
            return ['erro' => "Erro de segurança. Recarregue a página."];
        }

        if (!$this->editar($dados['id'])) {
            return ['erro' => "Página pública não encontrada."];
        }

        if (!$this->validarFormulario($dados, ['id', 'nome'])) {
            return ['erro' => "Preencha todos os campos obrigatórios."];
        }

        // Sanitização e validação aprimorada
        $dados['nome'] = trim($dados['nome']);
        if (strlen($dados['nome']) < 3 || strlen($dados['nome']) > 50) {
            return ['erro' => "O nome deve ter entre 3 e 50 caracteres."];
        }
        $dados['nome'] = htmlspecialchars($dados['nome'], ENT_QUOTES, 'UTF-8');

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

    /**
     * Valida os campos obrigatórios do formulário.
     */
    private function validarFormulario(array $dados, array $camposObrigatorios): bool
    {
        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo]) || empty(trim($dados[$campo]))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Registra erros no sistema.
     */
    private function registrarErro(string $mensagem, PDOException $e): void
    {
        $this->logger?->error("$mensagem: " . $e->getMessage());
    }

    /**
     * Valida um token CSRF.
     */
    private function validarCSRF(string $tokenCSRF): bool
    {
        return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $tokenCSRF);
    }
}
