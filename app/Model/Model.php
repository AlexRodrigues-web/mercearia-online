<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;
use PDOStatement;
use App\Model\ConexaoModel;

class Model extends ConexaoModel
{
    protected ?PDO $conn = null;
    private ?PDOStatement $query = null;
    private object $alerta;
    protected ?string $mensagem = null;

    public function __construct()
    {
        try {
            $this->conn = parent::conectar();
            if (!$this->conn instanceof PDO) {
                throw new PDOException("Falha ao conectar com o banco de dados.");
            }
        } catch (PDOException $e) {
            error_log("Erro na conexão do Model: " . $e->getMessage());
            throw new PDOException("Erro ao conectar com o banco de dados. Verifique os logs.");
        }

        $this->alerta = new \App\Model\AlertaModel();
    }

    public function buscarPermissoesUsuario(int $usuarioId): array
    {
        try {
            $sql = "SELECT p.nome FROM usuario_permissao up
                    JOIN permissao p ON up.permissao_id = p.id
                    WHERE up.usuario_id = :usuario_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();

            $permissoes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $permissoes ?: [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar permissões do usuário: " . $e->getMessage());
            return [];
        }
    }

    final protected function projetarTodos(string $query, array $parametros = []): array
    {
        try {
            $stmt = $this->conn->prepare($query);

            foreach ($parametros as $param => $valor) {
                $tipo = match (true) {
                    is_int($valor) => PDO::PARAM_INT,
                    is_bool($valor) => PDO::PARAM_BOOL,
                    is_null($valor) => PDO::PARAM_NULL,
                    default => PDO::PARAM_STR,
                };
                $stmt->bindValue(ltrim($param, ':'), $valor, $tipo);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erro em projetarTodos: " . $e->getMessage());
            return [];
        }
    }

    final public function projetarLista(string $query, array $parametros = []): array
    {
        try {
            $stmt = $this->conn->prepare($query);
            $this->parametros($stmt, $parametros);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erro em projetarLista: " . $e->getMessage());
            return [];
        }
    }

    final protected function projetarEspecifico(string $query, array $parametros = []): ?array
    {
        try {
            $stmt = $this->conn->prepare($query);

            foreach ($parametros as $param => $valor) {
                $tipo = match (true) {
                    is_int($valor) => PDO::PARAM_INT,
                    is_bool($valor) => PDO::PARAM_BOOL,
                    is_null($valor) => PDO::PARAM_NULL,
                    default => PDO::PARAM_STR,
                };
                $stmt->bindValue(ltrim($param, ':'), $valor, $tipo);
            }

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro em projetarEspecifico: " . $e->getMessage());
            return null;
        }
    }

    final protected function implementar(string $query, array $parametros = []): bool
{
    try {
        if (!$this->conn) {
            throw new PDOException("Conexão com o banco de dados não inicializada.");
        }

        $this->conn->beginTransaction();
        $stmt = $this->conn->prepare($query);
        $this->parametros($stmt, $parametros);
        $stmt->execute();
        $this->conn->commit();

        $linhasAfetadas = $stmt->rowCount();
        error_log("Linhas afetadas: $linhasAfetadas"); 
        return $linhasAfetadas > 0;

    } catch (PDOException $e) {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
        error_log("Erro em implementar: " . $e->getMessage());
        return false;
    }
}


    private function parametros(PDOStatement $stmt, array $parametros = []): void
    {
        foreach ($parametros as $parametro => $valor) {
            $this->valoresParam($stmt, $parametro, $valor);
        }
    }

    private function valoresParam(PDOStatement $stmt, string $parametro, mixed $valor): void
    {
        $tipo = match (true) {
            is_int($valor) => PDO::PARAM_INT,
            is_bool($valor) => PDO::PARAM_BOOL,
            is_null($valor) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };

        $stmt->bindValue(":$parametro", $valor, $tipo);
    }

    final protected function valida_int(mixed $campo, string $chave, string $mensagem, int $minimo): bool
    {
        $campo = filter_var($campo, FILTER_VALIDATE_INT);
        if ($campo === false || $campo < $minimo) {
            $this->registrarErroFormulario($chave, $mensagem);
            return false;
        }
        return true;
    }

    final protected function valida_float(mixed $campo, string $chave, string $mensagem, float $minimo): bool
    {
        $campo = filter_var($campo, FILTER_VALIDATE_FLOAT);
        if ($campo === false || $campo < $minimo) {
            $this->registrarErroFormulario($chave, $mensagem);
            return false;
        }
        return true;
    }

    private function registrarErroFormulario(string $chave, string $mensagem): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['Erro_form'][$chave] = $mensagem;
    }

    final protected function converteFloat(mixed $valor): float
    {
        $valor = str_replace(',', '.', str_replace('.', '', (string) $valor));
        return filter_var($valor, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }
}
