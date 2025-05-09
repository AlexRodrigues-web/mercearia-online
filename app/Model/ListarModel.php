<?php
declare(strict_types=1);

namespace App\Model;

class ListarModel extends Model
{
    /**
     * Lista todos os pedidos cadastrados no sistema.
     *
     * @return array Retorna um array de pedidos um array vazio caso não exista nenhum registro.
     */
    public function listarPedidos(): array
    {
        try {
            $sql = "SELECT p.id, p.produto_id, pr.nome AS produto, p.quantidade, c.nome AS cliente, p.data_pedido
                    FROM pedidos p
                    JOIN produtos pr ON p.produto_id = pr.id
                    JOIN clientes c ON p.cliente_id = c.id
                    ORDER BY p.data_pedido DESC";

            return $this->projetarTodos($sql);
        } catch (\Exception $e) {
            error_log("Erro ao listar pedidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista todos os clientes cadastrados no sistema.
     *
     * @return array Retorna um array de clientes ou um array vazio caso não existam registros.
     */
    public function listarClientes(): array
    {
        try {
            $sql = "SELECT id, nome, email, telefone, data_cadastro FROM clientes ORDER BY nome ASC";
            return $this->projetarTodos($sql);
        } catch (\Exception $e) {
            error_log("Erro ao listar clientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista todos os produtos cadastrados no sistema.
     *
     * @return array Retorna um array de produtos ou um array vazio caso não existam registros.
     */
    public function listarProdutos(): array
    {
        try {
            $sql = "SELECT id, nome, descricao, preco, estoque FROM produtos ORDER BY nome ASC";
            return $this->projetarTodos($sql);
        } catch (\Exception $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista registros de qualquer tabela no banco de dados.
     *
     * @param string $tabela Nome da tabela a ser consultada.
     * @param string $ordem Coluna para ordenação (opcional).
     * @param string $direcao Direção da ordenação ASC/DESC (opcional).
     * @return array Retorna um array de registros ou um array vazio caso não existam registros.
     */
    public function listarGenerico(string $tabela, string $ordem = "id", string $direcao = "ASC"): array
    {
        try {
            $sql = "SELECT * FROM {$tabela} ORDER BY {$ordem} {$direcao}";
            return $this->projetarTodos($sql);
        } catch (\Exception $e) {
            error_log("Erro ao listar registros da tabela '{$tabela}': " . $e->getMessage());
            return [];
        }
    }
}
