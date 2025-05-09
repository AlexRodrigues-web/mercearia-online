<?php

namespace App\Model;

use PDOException;

class Estoque extends Model
{
    /**
     * Lista todos os produtos no estoque.
     *
     * @return array Lista de produtos no estoque
     * @throws PDOException Lança exceção em caso de erro crítico
     */
    public function listar(): array
    {
        try {
            $produtos = parent::projetarTodos(
                "SELECT e.id, e.quantidade, p.nome AS produto_nome
                 FROM estoque e
                 INNER JOIN produto p ON e.produto_id = p.id
                 ORDER BY p.nome"
            );

            return $produtos ?: []; // Retorna array vazio caso não haja produtos
        } catch (PDOException $e) {
            error_log("❌ Erro ao listar estoque: " . $e->getMessage());
            throw new PDOException("Erro ao acessar os dados do estoque.");
        }
    }

    /**
     * Obtém os dados de um produto no estoque para edição.
     *
     * @param int $id ID do estoque
     * @return array Dados do produto ou lança exceção
     * @throws PDOException Lança exceção caso não encontre o produto
     */
    public function editar(int $id): array
    {
        try {
            $produto = parent::projetarEspecifico(
                "SELECT e.id, e.quantidade, p.nome AS produto_nome
                 FROM estoque e
                 INNER JOIN produto p ON e.produto_id = p.id
                 WHERE e.id = :id LIMIT 1",
                ['id' => $id],
                false
            );

            if (!$produto) {
                error_log("❌ Produto ID {$id} não encontrado no estoque.");
                throw new PDOException("Produto no estoque não encontrado.");
            }

            return $produto;
        } catch (PDOException $e) {
            error_log("❌ Erro ao buscar produto no estoque: " . $e->getMessage());
            throw new PDOException("Erro ao buscar o produto no estoque.");
        }
    }

    /**
     * Atualiza um produto no estoque.
     *
     * @param array $dados Dados do produto a serem atualizados
     * @return bool Retorna true se a atualização for bem-sucedida
     * @throws PDOException Lança exceção em caso de erro
     */
    public function atualizar(array $dados): bool
    {
        // Garantir que os índices necessários estão definidos
        if (!isset($dados['id'], $dados['produto_id'], $dados['quantidade'])) {
            throw new PDOException("Dados incompletos para atualização do estoque.");
        }

        // Sanitização e validação dos valores de entrada
        $id = filter_var($dados['id'], FILTER_VALIDATE_INT);
        $produto_id = filter_var($dados['produto_id'], FILTER_VALIDATE_INT);
        $quantidade = filter_var($dados['quantidade'], FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            throw new PDOException("ID do estoque inválido.");
        }

        if ($produto_id === false || $produto_id <= 0) {
            throw new PDOException("ID do produto inválido.");
        }

        if ($quantidade === false || $quantidade < 0) {
            throw new PDOException("Quantidade inválida. Deve ser um número inteiro positivo.");
        }

        // Verifica se o produto realmente existe antes de atualizar o estoque
        $produtoExiste = parent::projetarEspecifico(
            "SELECT id FROM produto WHERE id = :produto_id",
            ['produto_id' => $produto_id],
            true
        );

        if (!$produtoExiste) {
            throw new PDOException("Erro: Produto não encontrado. Verifique se o ID do produto é válido.");
        }

        try {
            return parent::implementar(
                "UPDATE estoque 
                 SET produto_id = :produto_id, quantidade = :quantidade 
                 WHERE id = :id",
                [
                    'id' => $id,
                    'produto_id' => $produto_id,
                    'quantidade' => $quantidade
                ]
            );
        } catch (PDOException $e) {
            error_log("❌ Erro ao atualizar produto ID {$id} no estoque: " . $e->getMessage());
            throw new PDOException("Erro ao atualizar o produto no estoque.");
        }
    }
}
