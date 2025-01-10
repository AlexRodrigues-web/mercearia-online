<?php

namespace App\Model;

class Paginas extends Model
{
    private array $paginas = [];
    private array $pgPublicas = [];

    final public function acessoPaginas(array $id): array
    {
        try {
            $this->paginas = parent::projetarExpecifico(
                "SELECT p.id, p.nome
                 FROM funcionario_pg_privada f
                 INNER JOIN pg_privada p ON f.pg_privada_id = p.id
                 WHERE f.funcionario_id = :id",
                $id,
                false
            );

            $this->paginas = $this->transformarArrayEmVetor($this->paginas);
            return $this->paginas;
        } catch (\PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao acessar páginas privadas: " . $e->getMessage());
            return [];
        }
    }

    final public function listaPgPublicas(): array
    {
        try {
            $this->pgPublicas = parent::projetarTodos(
                "SELECT id, nome FROM pg_publica ORDER BY nome ASC"
            );

            $this->pgPublicas = $this->transformarArrayEmVetor($this->pgPublicas);
            return $this->pgPublicas;
        } catch (\PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar páginas públicas: " . $e->getMessage());
            return [];
        }
    }

    private function transformarArrayEmVetor(array $paginas): array
    {
        $vetor = [];
        foreach ($paginas as $pagina) {
            $vetor[] = $pagina['nome'];
        }
        return $vetor;
    }
}
