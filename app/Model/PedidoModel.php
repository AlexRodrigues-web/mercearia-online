<?php
namespace App\Model;

use PDO;

class PedidoModel extends Model
{
    public function finalizarPedido(array $pedido): array|bool
    {
        error_log("[PedidoModel] Iniciando finalização do pedido...");
        error_log("[PedidoModel] Dados do pedido recebidos: " . print_r($pedido, true));

        try {
            $conn = $this->conectar();
            error_log("[PedidoModel] Conectado ao banco de dados com sucesso.");

            $stmt = $conn->prepare("INSERT INTO pedido (cliente_id, valor_total, metodo_pagamento, data_pedido) 
                                    VALUES (:cliente_id, :valor_total, :metodo_pagamento, NOW())");
            $stmt->bindValue(':cliente_id', $pedido['cliente_id'], PDO::PARAM_INT);
            $stmt->bindValue(':valor_total', $pedido['valor_total']);
            $stmt->bindValue(':metodo_pagamento', $pedido['metodo_pagamento']);
            $stmt->execute();

            $pedidoId = $conn->lastInsertId();
            error_log("[PedidoModel] Pedido inserido com ID: " . $pedidoId);

            $itemStmt = $conn->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) 
                                        VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)");

            foreach ($pedido['produto'] as $produtoId => $item) {
                $itemStmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
                $itemStmt->bindValue(':produto_id', $produtoId, PDO::PARAM_INT);
                $itemStmt->bindValue(':quantidade', $item['quantidade'], PDO::PARAM_INT);
                $itemStmt->bindValue(':preco_unitario', $item['preco']);
                $itemStmt->execute();
                error_log("[PedidoModel] Item adicionado - Produto ID: {$produtoId}, Quantidade: {$item['quantidade']}");
            }

            error_log("[PedidoModel] Todos os itens inseridos com sucesso.");
            return ['id' => $pedidoId];

        } catch (\Exception $e) {
            error_log("[PedidoModel] Erro ao finalizar pedido: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPedidosPorCliente(int $clienteId): array
    {
        error_log("[PedidoModel] Buscando pedidos do cliente ID: {$clienteId}");

        try {
            $conn = $this->conectar();

            $stmt = $conn->prepare("SELECT * FROM pedido WHERE cliente_id = :cliente_id ORDER BY data_pedido DESC");
            $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("[PedidoModel] Pedidos encontrados: " . count($resultados));

            return $resultados;
        } catch (\Exception $e) {
            error_log("[PedidoModel] Erro ao buscar pedidos: " . $e->getMessage());
            return [];
        }
    }
}
