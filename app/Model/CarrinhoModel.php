<?php

declare(strict_types=1);

namespace App\Model;

use PDOException;

class CarrinhoModel extends Model
{
    /**
     * Busca um produto pelo ID, incluindo promoções ativas e estoque
     */
    public function buscarProdutoPorId(int $id): ?array
    {
        try {
            $sql = "
                SELECT 
                    p.id, 
                    p.nome, 
                    p.preco,
                    p.estoque,
                    p.imagem,
                    pr.tipo AS tipo_promocao,
                    pr.desconto AS valor_promocional,
                    pr.inicio,
                    pr.fim
                FROM produto p
                LEFT JOIN promocao pr 
                    ON pr.produto_id = p.id 
                    AND pr.ativo = 1
                    AND pr.inicio <= NOW() 
                    AND pr.fim >= NOW()
                WHERE p.id = :id
                LIMIT 1
            ";

            $resultado = $this->projetarLista($sql, ['id' => $id]);

            $produto = $resultado[0] ?? null;

            if (!$produto) {
                return null;
            }

            // Aplica o valor da promoção se válida
            if (!empty($produto['tipo_promocao']) && $produto['valor_promocional'] > 0) {
                if ($produto['tipo_promocao'] === 'percentual') {
                    $produto['preco'] = $produto['preco'] * (1 - $produto['valor_promocional'] / 100);
                } elseif ($produto['tipo_promocao'] === 'fixo') {
                    $produto['preco'] = $produto['valor_promocional'];
                }

                // Garante que o preço fique com 2 casas decimais
                $produto['preco'] = round((float) $produto['preco'], 2);
            }

            return $produto;
        } catch (PDOException $e) {
            error_log("[CarrinhoModel] Erro ao buscar produto ID $id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica se a quantidade desejada está disponível em estoque
     */
    public function verificarEstoqueDisponivel(int $id, int $quantidade): bool
    {
        try {
            $resultado = $this->projetarLista(
                "SELECT estoque FROM produto WHERE id = :id LIMIT 1",
                ['id' => $id]
            );

            return !empty($resultado[0]) && $resultado[0]['estoque'] >= $quantidade;
        } catch (PDOException $e) {
            error_log("[CarrinhoModel] Erro ao verificar estoque do produto $id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Simula o cálculo de frete com base no código postal (Portugal)
     */
    public function calcularFretePorCodigoPostal(string $codigoPostal): array
    {
        if (!preg_match('/^\d{4}-\d{3}$/', $codigoPostal)) {
            return [
                'status' => 'erro',
                'mensagem' => 'Código postal inválido. Formato esperado: 1000-001'
            ];
        }

        $zona = intval(substr($codigoPostal, 0, 4));
        $valor = ($zona < 2000) ? 2.50 : 4.90;
        $prazo = ($zona < 2000) ? '1 a 2 dias úteis' : '3 a 5 dias úteis';

        return [
            'status' => 'ok',
            'valor' => $valor,
            'prazo' => $prazo,
            'mensagem' => "Entrega estimada em {$prazo} com custo de €" . number_format($valor, 2, ',', '.')
        ];
    }
}
