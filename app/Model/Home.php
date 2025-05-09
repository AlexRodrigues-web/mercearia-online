<?php

namespace App\Model;

use PDOException;

class Home extends Model
{
    /**
     * Retorna estatísticas principais para o painel inicial.
     *
     * @return array Estatísticas do sistema
     */
    public function listar(): array
    {
        try {
            // Verifica se a classe pai possui o método necessário
            if (!method_exists($this, 'projetarEspecifico')) {
                throw new PDOException("Método projetarEspecifico() não encontrado na classe pai.");
            }

            $dados = parent::projetarEspecifico("
                SELECT 
                    COUNT(DISTINCT f.id) AS total_funcionarios,
                    COUNT(DISTINCT CASE WHEN f.ativo = 1 THEN f.id END) AS funcionarios_ativos,
                    COUNT(DISTINCT fr.id) AS total_fornecedores,
                    COUNT(DISTINCT e.id) AS total_produtos,
                    COALESCE(SUM(ca.total), 0) AS total_vendas,

                    (SELECT fr.nome FROM fornecedor fr
                     LEFT JOIN produto p ON p.fornecedor_id = fr.id
                     LEFT JOIN estoque e ON e.produto_id = p.id
                     GROUP BY fr.id
                     ORDER BY SUM(e.quantidade) DESC
                     LIMIT 1) AS maior_fornecedor,

                    (SELECT p.nome FROM produtos_caixa pc
                     LEFT JOIN produto p ON pc.produto_id = p.id
                     GROUP BY p.id
                     ORDER BY SUM(pc.quantidade) DESC
                     LIMIT 1) AS produto_mais_vendido,

                    COALESCE(SUM(e.quantidade), 0) AS estoque_total,
                    COUNT(DISTINCT ca.id) AS total_caixas
                FROM funcionario f
                LEFT JOIN fornecedor fr ON fr.id IS NOT NULL
                LEFT JOIN estoque e ON e.id IS NOT NULL
                LEFT JOIN caixa ca ON ca.id IS NOT NULL
            ", [], false);

            return $dados ?: $this->retornarValoresPadrao();
        } catch (PDOException $e) {
            return $this->tratarErro("Erro ao carregar dados do painel", $e);
        }
    }

    /**
     * Retorna valores padrão para evitar falhas no painel.
     *
     * @return array Dados padrão do painel
     */
    private function retornarValoresPadrao(): array
    {
        return [
            'total_funcionarios' => 0,
            'funcionarios_ativos' => 0,
            'total_fornecedores' => 0,
            'total_produtos' => 0,
            'total_vendas' => 0.0,
            'maior_fornecedor' => 'Nenhum fornecedor',
            'produto_mais_vendido' => 'Nenhum produto vendido',
            'estoque_total' => 0,
            'total_caixas' => 0
        ];
    }

    /**
     * Trata erros e retorna valores padrão para manter a página funcional.
     *
     * @param string $mensagem Mensagem personalizada do erro
     * @param PDOException $e Exceção capturada
     * @return array Retorna um array seguro para manter a interface funcional
     */
    private function tratarErro(string $mensagem, PDOException $e): array
    {
        // Verifica se a sessão está iniciada antes de definir mensagens de erro
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o método de alerta está disponível
        if (method_exists($this, 'alertaFalha')) {
            $_SESSION['msg_erro'][] = parent::alertaFalha($mensagem);
        } else {
            $_SESSION['msg_erro'][] = "Erro no sistema. Contate o suporte.";
        }

        error_log("[ERRO] $mensagem: " . $e->getMessage());
        return $this->retornarValoresPadrao();
    }
}
