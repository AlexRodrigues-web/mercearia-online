<?php
declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;

class ProdutoModel extends Conexao
{
    public function buscarTodos(): array
    {
        try {
            $stmt = $this->conectar()->query("SELECT * FROM produtos");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }
}
