<?php
declare(strict_types=1);

namespace App\Model;

use PDOException;
use Psr\Log\LoggerInterface;

class NivelModel extends Model
{
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getConnection(): \PDO
    {
        return $this->conn;
    }

    public function listar(): array
    {
        try {
            return parent::projetarTodos(
                "SELECT id, nome, status FROM nivel ORDER BY id ASC"
            ) ?: [];
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
                "SELECT id, nome, status FROM nivel WHERE id = :id LIMIT 1",
                ['id' => $id]
            ) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar nível: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar(array $dados): bool
    {
        if (!$this->validarCampos($dados)) {
            return false;
        }

        try {
            $resultado = parent::implementar(
                "INSERT INTO nivel (nome, status) VALUES (:nome, :status)",
                [
                    'nome'   => $dados['nome'],
                    'status' => $dados['status']
                ]
            );
            return $resultado !== false;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao cadastrar nível: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(array $dados): bool
    {
        if (!$this->validarCampos($dados, true)) {
            return false;
        }

        try {
            $stmt = $this->getConnection()->prepare(
                "UPDATE nivel SET nome = :nome, status = :status WHERE id = :id"
            );
            $executou = $stmt->execute([
                'id'     => $dados['id'],
                'nome'   => $dados['nome'],
                'status' => $dados['status']
            ]);

            error_log(
                "SQL executado. Sucesso? " . ($executou ? "SIM" : "NÃO")
                . " | Linhas afetadas: " . $stmt->rowCount()
            );
            return $executou;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar nível: " . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            return false;
        }

        try {
            $dep = parent::projetarEspecifico(
                "SELECT COUNT(*) AS total FROM usuarios WHERE nivel_id = :id",
                ['id' => $id]
            );
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao verificar dependências: " . $e->getMessage());
            return false;
        }

        if ($dep && $dep['total'] > 0) {
            return false;
        }

        try {
            $resultado = parent::implementar(
                "DELETE FROM nivel WHERE id = :id",
                ['id' => $id]
            );
            return (bool) $resultado;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir nível: " . $e->getMessage());
            return false;
        }
    }

    private function validarCampos(array &$dados, bool $atualizacao = false): bool
    {
        if ($atualizacao) {
            if (!isset($dados['id'])
                || !filter_var($dados['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])
            ) {
                return false;
            }
        }

        $nome   = trim((string)($dados['nome'] ?? ''));
        $status = trim((string)($dados['status'] ?? ''));

        if ($nome === '' || mb_strlen($nome) < 3 || mb_strlen($nome) > 20) {
            return false;
        }
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        $dados['nome']   = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $dados['status'] = $status;
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
}
