<?php
declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Model\ConexaoModel;

class VendaModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = ConexaoModel::conectar();
    }

    public function buscarTodas(): array
    {
        $sql = "SELECT * FROM vendas ORDER BY data_venda DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM vendas WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function criarVenda(array $dados): bool
    {
        $sql = "INSERT INTO vendas (cliente_id, total, data_venda) VALUES (:cliente_id, :total, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":cliente_id", $dados['cliente_id'], PDO::PARAM_INT);
        $stmt->bindValue(":total", $dados['total'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function atualizarVenda(int $id, array $dados): bool
    {
        $sql = "UPDATE vendas SET cliente_id = :cliente_id, total = :total WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":cliente_id", $dados['cliente_id'], PDO::PARAM_INT);
        $stmt->bindValue(":total", $dados['total'], PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletarVenda(int $id): bool
    {
        $sql = "DELETE FROM vendas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
