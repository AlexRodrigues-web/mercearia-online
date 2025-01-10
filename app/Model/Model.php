<?php

namespace App\Model;

use PDO;
use PDOException;

class Model extends Conexao
{
    protected $conn;
    private $query;
    private object $alerta;
    protected $mensagem;
    private $resultado;

    public function __construct()
    {
        $this->conn = parent::conectar();
        $this->alerta = new \App\Model\Alerta();
    }

    final protected function alertaFalha(string $mensagem): string
    {
        $this->mensagem = $this->alerta->alertaFalha($mensagem);
        return $this->mensagem;
    }

    final protected function alertaSucesso(string $mensagem): string
    {
        $this->mensagem = $this->alerta->alertaSucesso($mensagem);
        return $this->mensagem;
    }

    final protected function alertaBemvindo(string $mensagem): string
    {
        $this->mensagem = $this->alerta->alertaBemvindo($mensagem);
        return $this->mensagem;
    }

    final protected function projetarTodos(string $query): array
    {
        $this->query = $this->conn->prepare($query);
        $this->query->execute();
        return $this->query->fetchAll(PDO::FETCH_ASSOC);
    }

    final protected function projetarExpecifico(string $query, array $parametros = [], bool $unico = true)
    {
        $this->query = $this->conn->prepare($query);
        $this->parametros($this->query, $parametros);
        $this->query->execute();

        return $unico ? $this->query->fetch(PDO::FETCH_ASSOC) : $this->query->fetchAll(PDO::FETCH_ASSOC);
    }

    final protected function implementar(string $query, array $parametros = []): void
    {
        $this->query = $this->conn->prepare($query);
        $this->parametros($this->query, $parametros);
        $this->query->execute();
    }

    final protected function existeCamposFormulario(array $dados, array $obrigatorio, int $tamanho): bool
    {
        foreach ($obrigatorio as $campo) {
            if (!isset($dados[$campo]) || empty(trim($dados[$campo]))) {
                return false;
            }
        }
        return count($dados) >= $tamanho;
    }

    final protected function formularioValido(array $validacao): bool
    {
        foreach ($validacao as $valido) {
            if (!$valido) {
                return false;
            }
        }
        return true;
    }

    private function parametros($query, array $parametros = []): void
    {
        foreach ($parametros as $parametro => $valor) {
            $this->valoresParam($query, $parametro, $valor);
        }
    }

    private function valoresParam($query, string $parametro, $valor): void
    {
        $query->bindValue(":$parametro", $valor);
    }

    final protected function valida_int($campo, string $chave, string $mensagem, int $minimo): bool
    {
        $campo = intval($campo);
        if ($campo < $minimo) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    final protected function valida_float($campo, string $chave, string $mensagem, float $minimo): bool
    {
        $campo = $this->converteFloat($campo);
        if ($campo < $minimo) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    final protected function valida_date($campo, string $chave, string $mensagem): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', $campo);
        if (!$date || $date->format('Y-m-d') !== $campo) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    final protected function valida_tamanho(string $variavel, string $chave, string $mensagem, int $maximo, int $minimo): bool
    {
        $variavel = trim($variavel);
        if (strlen($variavel) < $minimo || strlen($variavel) > $maximo) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    final protected function valida_bool($variavel, string $chave, string $mensagem): bool
    {
        if (!is_bool($variavel)) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    final protected function converteFloat($valor): float
    {
        $valor = str_replace(['.', ','], ['', '.'], $valor);
        return floatval($valor);
    }
}
