<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;
use Psr\Log\LoggerInterface;

class Nivel extends Model
{
    private array $formObrigatorio = ['nome'];
    private int $formObrigatorioQuantidade = 1;
    private array $formValido = [];
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->iniciarSessao();
    }

    private function iniciarSessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function getBaseUrl(): string
    {
        return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
    }

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome FROM nivel ORDER BY id ASC") ?: [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar níveis: " . $e->getMessage());
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
                "SELECT id, nome FROM nivel WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar nível: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar(array $dados): void
    {
        if (!$this->validarCampos($dados)) {
            return;
        }

        try {
            parent::implementar("INSERT INTO nivel (nome) VALUES (:nome)", ['nome' => $dados['nome']]);
            $this->mensagemSucesso("Nível cadastrado com sucesso!");
            $this->redirecionar('nivel/listar');
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao cadastrar nível: " . $e->getMessage());
            $this->mensagemErro("Erro ao cadastrar nível.");
        }
    }

    public function excluir(int $id): void
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            $this->mensagemErro("ID inválido.");
            return;
        }

        $dependencias = parent::projetarEspecifico(
            "SELECT COUNT(*) AS total FROM usuarios WHERE nivel_id = :id",
            ['id' => $id]
        );

        if ($dependencias && $dependencias['total'] > 0) {
            $this->mensagemErro("Este nível está vinculado a usuários e não pode ser excluído.");
            return;
        }

        try {
            parent::implementar("DELETE FROM nivel WHERE id = :id", ['id' => $id]);
            $this->mensagemSucesso("Nível excluído com sucesso!");
            $this->redirecionar('nivel/listar');
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir nível: " . $e->getMessage());
            $this->mensagemErro("Erro ao excluir nível.");
        }
    }

    public function atualizar(array $dados): void
    {
        if (!$this->validarCampos($dados, true)) {
            return;
        }

        try {
            parent::implementar("UPDATE nivel SET nome = :nome WHERE id = :id", [
                'id' => $dados['id'],
                'nome' => $dados['nome']
            ]);

            $this->mensagemSucesso("Nível atualizado com sucesso!");
            $this->redirecionar('nivel/listar');
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar nível: " . $e->getMessage());
            $this->mensagemErro("Erro ao atualizar nível.");
        }
    }

    private function validarCampos(array &$dados, bool $atualizacao = false): bool
    {
        if ($atualizacao) {
            if (!isset($dados['id']) || !filter_var($dados['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
                $this->mensagemErro("ID inválido.");
                return false;
            }
        }

        if (empty(trim($dados['nome'])) || strlen(trim($dados['nome'])) < 3) {
            $this->mensagemErro("O nome do nível deve ter pelo menos 3 caracteres.");
            return false;
        }

        if (strlen($dados['nome']) > 20) {
            $this->mensagemErro("O nome do nível deve ter no máximo 20 caracteres.");
            return false;
        }

        $dados['nome'] = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        return true;
    }

    private function mensagemErro(string $mensagem): void
    {
        $_SESSION['msg'] = parent::alertaFalha($mensagem);
    }

    private function mensagemSucesso(string $mensagem): void
    {
        $_SESSION['msg'] = parent::alertaSucesso($mensagem);
    }

    private function redirecionar(string $rota): void
    {
        header("Location: " . $this->getBaseUrl() . $rota);
        exit();
    }

    private function registrarErro(string $mensagem): void
    {
        if ($this->logger) {
            $this->logger->error($mensagem);
        } else {
            error_log($mensagem);
        }
    }
}
