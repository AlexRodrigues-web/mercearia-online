<?php

namespace App\Model;

use PDOException;

class Home extends Model
{
    public function listar(): array
    {
        try {
            $home = parent::projetarTodos(
                "SELECT 
                    (SELECT COUNT(*) FROM funcionario) AS total_funcionarios,
                    (SELECT COUNT(*) FROM funcionario WHERE ativo = 1) AS funcionarios_ativos,
                    (SELECT COUNT(*) FROM fornecedor) AS total_fornecedores,
                    (SELECT COUNT(*) FROM estoque) AS total_produtos,
                    (SELECT SUM(c.total) FROM caixa c) AS total_vendas,
                    
                    (SELECT f.nome 
                     FROM fornecedor f
                     INNER JOIN produto p ON p.fornecedor_id = f.id
                     INNER JOIN estoque e ON e.produto_id = p.id
                     GROUP BY f.id
                     ORDER BY SUM(e.quantidade) DESC
                     LIMIT 1) AS maior_fornecedor,

                    (SELECT p.nome
                     FROM caixa c
                     INNER JOIN produto p ON c.produto_id = p.id
                     GROUP BY p.id
                     ORDER BY SUM(c.quantidade) DESC
                     LIMIT 1) AS produto_mais_vendido,

                    (SELECT SUM(e.quantidade)
                     FROM estoque e) AS estoque_total,

                    (SELECT COUNT(*) FROM caixa) AS total_caixas
                FROM dual"
            );

            return $home;
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao carregar dados do painel: " . $e->getMessage());
            return [];
        }
    }
}
