<?php

namespace App\Model;

use PDOException;

class ProdutoAdminModel extends Model
{
    public function listarTodosProdutos(): array
    {
        try {
            $query = "SELECT * FROM produto ORDER BY id DESC";
            return $this->projetarTodos($query) ?? [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar produtos ADM", $e);
            return [];
        }
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            $query = "SELECT * FROM produto WHERE id = :id LIMIT 1";
            return $this->projetarEspecifico($query, ['id' => $id]) ?: null;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar produto ID {$id}", $e);
            return null;
        }
    }

    public function salvarProduto(array $dados): bool
    {
        try {
            $dados['estoque'] = $dados['quantidade'] ?? 0;
            unset($dados['quantidade'], $dados['entrada_manual'], $dados['saida_manual'], $dados['local_armazenagem']);

            $query = "
                INSERT INTO produto
                (nome, descricao, preco, estoque, categoria, unidade, sku, validade, estoque_minimo, local,
                 fornecedor_id, custo, nipc, status, imagem, dt_registro)
                VALUES
                (:nome, :descricao, :preco, :estoque, :categoria, :unidade, :sku, :validade, :estoque_minimo, :local,
                 :fornecedor_id, :custo, :nipc, :status, :imagem, :dt_registro)
            ";

            // Mapeamento dos dados
            $params = [
                'nome'            => $dados['nome'] ?? null,
                'descricao'       => $dados['descricao'] ?? null,
                'preco'           => $dados['preco'] ?? 0,
                'estoque'         => $dados['estoque'] ?? 0,
                'categoria'       => $dados['categoria'] ?? 'Outros',
                'unidade'         => $dados['unidade'] ?? null,
                'sku'             => $dados['sku'] ?? null,
                'validade'        => $dados['validade'] ?? null,
                'estoque_minimo'  => $dados['estoque_minimo'] ?? 0,
                'local'           => $dados['local'] ?? null,
                'fornecedor_id'   => $dados['fornecedor_id'] ?? null,
                'custo'           => $dados['custo'] ?? null,
                'nipc'            => $dados['nipc'] ?? null,
                'status'          => $dados['status'] ?? 'ativo',
                'imagem'          => $dados['imagem'] ?? null,
                'dt_registro'     => $dados['dt_registro'] ?? date('Y-m-d H:i:s'),
            ];

            return $this->implementar($query, $params);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao salvar novo produto", $e);
            return false;
        }
    }

    public function editarProduto(array $dados): bool
    {
        try {
            $dados['estoque'] = $dados['quantidade'] ?? 0;
            unset($dados['quantidade'], $dados['entrada_manual'], $dados['saida_manual'], $dados['local_armazenagem']);

            $campos = "
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                estoque = :estoque,
                categoria = :categoria,
                unidade = :unidade,
                sku = :sku,
                validade = :validade,
                estoque_minimo = :estoque_minimo,
                local = :local,
                fornecedor_id = :fornecedor_id,
                custo = :custo,
                nipc = :nipc,
                status = :status
            ";

            if (!empty($dados['imagem'])) {
                $campos .= ", imagem = :imagem";
            } else {
                unset($dados['imagem']);
            }

            $query = "UPDATE produto SET {$campos} WHERE id = :id";

            return $this->implementar($query, $dados);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao editar produto ID {$dados['id']}", $e);
            return false;
        }
    }

    public function excluirProduto(int $id): bool
    {
        try {
            $query = "DELETE FROM produto WHERE id = :id";
            return $this->implementar($query, ['id' => $id]);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao excluir produto ID {$id}", $e);
            return false;
        }
    }

    public function listarFornecedores(): array
    {
        try {
            $query = "SELECT id, nome FROM fornecedor WHERE ativo = 1 ORDER BY nome";
            return $this->projetarTodos($query) ?? [];
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar fornecedores", $e);
            return [];
        }
    }

    private function registrarErro(string $mensagem, PDOException $e): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['msg_erro'][] = $mensagem;
        error_log("[ProdutoAdminModel] {$mensagem}: " . $e->getMessage());
    }
}
