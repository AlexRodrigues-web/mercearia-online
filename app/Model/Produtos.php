<?php

namespace App\Model;

use PDOException;

class Produtos extends Model
{
    private function setMessage(string $tipo, string $mensagem): void
    {
        $tipo = ucfirst(strtolower($tipo)); // Transforma "falha" -> "Falha" e "sucesso" -> "Sucesso"
        if (!isset($_SESSION['msg'])) {
            $_SESSION['msg'] = [];
        }
        $_SESSION['msg'][] = parent::{"alerta$tipo"}($mensagem);
    }

    public function listarProdutos(): array
    {
        try {
            return parent::projetarTodos("
                SELECT p.id, p.nome, p.preco, f.nome AS fornecedor, c.barra AS codigo, e.quantidade
                FROM produto p
                INNER JOIN fornecedor f ON p.fornecedor_id = f.id
                LEFT JOIN codigo c ON p.codigo_id = c.id
                LEFT JOIN estoque e ON p.id = e.produto_id
                ORDER BY p.nome ASC
            ") ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }

    public function editarProduto(int $id): array
    {
        try {
            return parent::projetarEspecifico("
                SELECT p.id, p.nome, p.preco, f.id AS fornecedor_id, f.nome AS fornecedor, 
                       c.barra AS codigo, e.quantidade
                FROM produto p
                INNER JOIN fornecedor f ON p.fornecedor_id = f.id
                LEFT JOIN codigo c ON p.codigo_id = c.id
                LEFT JOIN estoque e ON p.id = e.produto_id
                WHERE p.id = :id LIMIT 1",
                ['id' => $id]
            ) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar produto: " . $e->getMessage());
            return [];
        }
    }

    public function atualizarProduto(array $dados): void
    {
        $dados = $this->sanitizarDados($dados);
        if (!$dados) {
            return;
        }

        try {
            parent::implementar("
                UPDATE produto 
                SET nome = :nome, preco = :preco, fornecedor_id = :fornecedor_id
                WHERE id = :id",
                $dados
            );

            parent::implementar("
                UPDATE estoque 
                SET quantidade = :quantidade 
                WHERE produto_id = :id",
                ['quantidade' => $dados['quantidade'], 'id' => $dados['id']]
            );

            $this->setMessage('Sucesso', "Produto atualizado com sucesso!");
        } catch (PDOException $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            $this->setMessage('Falha', "Erro ao atualizar o produto.");
        }
    }

    private function sanitizarDados(array $dados): ?array
    {
        if (!isset($dados['nome'], $dados['preco'], $dados['fornecedor_id'], $dados['quantidade'], $dados['id'])) {
            $this->setMessage('Falha', "Todos os campos são obrigatórios.");
            return null;
        }

        $dados['id'] = filter_var($dados['id'], FILTER_VALIDATE_INT);
        $dados['nome'] = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        $dados['preco'] = filter_var($dados['preco'], FILTER_VALIDATE_FLOAT);
        $dados['fornecedor_id'] = filter_var($dados['fornecedor_id'], FILTER_VALIDATE_INT);
        $dados['quantidade'] = filter_var($dados['quantidade'], FILTER_VALIDATE_INT);

        if ($dados['id'] === false || $dados['id'] <= 0) {
            $this->setMessage('Falha', "ID do produto inválido.");
            return null;
        }

        if (strlen($dados['nome']) < 2 || strlen($dados['nome']) > 30) {
            $this->setMessage('Falha', "O nome do produto deve ter entre 2 e 30 caracteres.");
            return null;
        }

        if ($dados['preco'] === false || $dados['preco'] < 0.10) {
            $this->setMessage('Falha', "O preço deve ser maior que R$ 0,10.");
            return null;
        }

        if ($dados['quantidade'] === false || $dados['quantidade'] < 0) {
            $this->setMessage('Falha', "A quantidade deve ser um número inteiro positivo.");
            return null;
        }

        return $dados;
    }
}
