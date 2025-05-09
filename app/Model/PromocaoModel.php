<?php

namespace App\Model;

use PDO;
use PDOException;

class PromocaoModel extends Model
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function buscarProdutosPromocionaisAleatorios(int $limite = 5): array
    {
        try {
            $sql = "
                SELECT 
                    p.id,
                    p.nome,
                    p.preco,
                    p.imagem,
                    promo.desconto,
                    promo.inicio,
                    promo.fim,
                    promo.tipo,
                    promo.selo,
                    promo.vis_home,
                    promo.vis_banner,
                    promo.vis_pagina
                FROM promocao AS promo
                INNER JOIN produto AS p ON p.id = promo.produto_id
                WHERE promo.ativo = 1
                  AND promo.inicio <= NOW()
                  AND promo.fim >= NOW()
                ORDER BY RAND()
                LIMIT :limite
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("📦 [PromocaoModel] Total promoções encontradas: " . count($produtos));
            return $produtos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar promoções reais: " . $e->getMessage());
            return [];
        }
    }
}
