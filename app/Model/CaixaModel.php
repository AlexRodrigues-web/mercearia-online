<?php 
namespace App\Model;

use PDOException;

class CaixaModel extends Model
{   
    public function finalizarCompra(array $dados): array
    {
        try {
            if ($this->conn->inTransaction() === false) {
                $this->conn->beginTransaction();
            }

            $clienteId = $dados['cliente_id'] ?? null;
            $itens = $dados['itens'] ?? [];
            $total = $dados['total'] ?? 0;
            $pagamento = $dados['pagamento'] ?? '';
            $valorPago = $dados['valor_pago'] ?? 0;
            $troco = $dados['troco'] ?? 0;
            $data = date('Y-m-d H:i:s');

            if (!$clienteId || empty($itens) || $total <= 0 || empty($pagamento)) {
                return ['erro' => 'Dados incompletos para finalizar a compra.'];
            }

            // Registrar os produtos no pedido
            foreach ($itens as $item) {
                $this->registrarProduto(
                    $clienteId,
                    (int)$item['id'],
                    (int)$item['quantidade'],
                    (float)$item['subtotal'],
                    $data
                );
            }

            // Registrar venda principal
            parent::implementar(
                "INSERT INTO caixa (cliente_id, total, pagamento, valor, troco, dt_registro) 
                VALUES (:cliente_id, :total, :pagamento, :valor, :troco, :dt_registro)",
                [
                    'cliente_id' => $clienteId,
                    'total' => $total,
                    'pagamento' => $pagamento,
                    'valor' => $valorPago,
                    'troco' => $troco,
                    'dt_registro' => $data
                ]
            );

            $this->conn->commit();
            return ['sucesso' => 'Compra finalizada com sucesso!'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro ao finalizar compra: " . $e->getMessage());
            return ['erro' => 'Erro ao processar a venda. Tente novamente.'];
        }
    }

    private function registrarProduto(int $clienteId, int $produtoId, int $quantidade, float $subtotal, string $data): void
    {
        $produto = parent::projetarEspecifico(
            "SELECT quantidade FROM estoque WHERE produto_id = :produto_id",
            ['produto_id' => $produtoId],
            true
        );

        if (!$produto || $produto['quantidade'] < $quantidade) {
            throw new PDOException("Estoque insuficiente para o produto ID {$produtoId}");
        }

        // Atualiza o estoque
        $novoEstoque = $produto['quantidade'] - $quantidade;
        parent::implementar(
            "UPDATE estoque SET quantidade = :quantidade WHERE produto_id = :produto_id",
            [
                'quantidade' => $novoEstoque,
                'produto_id' => $produtoId
            ]
        );

        // Registra produto no caixa
        parent::implementar(
            "INSERT INTO produtos_caixa (cliente, produto_id, quantidade, subTotal, dt_registro) 
             VALUES (:cliente, :produto_id, :quantidade, :subTotal, :dt_registro)",
            [
                'cliente' => $clienteId,
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'subTotal' => $subtotal,
                'dt_registro' => $data
            ]
        );
    }
}
