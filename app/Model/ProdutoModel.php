<?php

namespace App\Model;

use PDOException;

class ProdutoModel extends Model
{
    public function listar(): array
    {
        return $this->listarTodosProdutos();
    }

    public function listarTodosProdutos(): array
    {
        try {
            error_log("[ProdutoModel] Executando listarTodosProdutos()");

            $query = "
                SELECT p.id,
                       p.nome,
                       p.descricao,
                       p.preco,
                       p.categoria,
                       p.imagem,
                       COALESCE(f.nome, 'Fornecedor Desconhecido') AS fornecedor,
                       p.kilograma,
                       p.litro
                FROM produto p
                LEFT JOIN fornecedor f ON p.fornecedor_id = f.id
                WHERE p.status = 'ativo' AND p.estoque > 0
                ORDER BY p.nome
            ";

            $produtos = $this->projetarTodos($query) ?? [];
            error_log("[ProdutoModel] Produtos encontrados: " . count($produtos));

            foreach ($produtos as &$produto) {
                $nomeImagem = basename($produto['imagem'] ?? '');
                $caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/" . $nomeImagem;

                $produto['imagem'] = (!empty($nomeImagem) && file_exists($caminhoImagem))
                    ? BASE_URL . "app/Assets/image/produtos/" . rawurlencode($nomeImagem)
                    : BASE_URL . "app/Assets/image/produtos/produto_default.jpg";

                if (!file_exists($caminhoImagem)) {
                    error_log("[ProdutoModel] Imagem não encontrada para o produto ID {$produto['id']} - {$produto['nome']}");
                }

                $produto['categoria'] = trim(mb_strtolower($produto['categoria'], 'UTF-8'));
            }

            return $produtos;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar produtos", $e);
            return [];
        }
    }

    public function buscarProdutos(string $termo): array
    {
        try {
            error_log("[ProdutoModel] Buscando produtos com o termo: $termo");

            $query = "
                SELECT p.id,
                       p.nome,
                       p.descricao,
                       p.preco,
                       p.categoria,
                       p.imagem,
                       COALESCE(f.nome, 'Fornecedor Desconhecido') AS fornecedor,
                       p.kilograma,
                       p.litro
                FROM produto p
                LEFT JOIN fornecedor f ON p.fornecedor_id = f.id
                WHERE p.nome LIKE :termo1
                   OR p.descricao LIKE :termo2
            ";

            $params = [
                ':termo1' => '%' . $termo . '%',
                ':termo2' => '%' . $termo . '%'
            ];

            $produtos = $this->projetarTodos($query, $params) ?? [];

            foreach ($produtos as &$produto) {
                $nomeImagem = basename($produto['imagem'] ?? '');
                $caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/" . $nomeImagem;

                $produto['imagem'] = (!empty($nomeImagem) && file_exists($caminhoImagem))
                    ? BASE_URL . "app/Assets/image/produtos/" . rawurlencode($nomeImagem)
                    : BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
            }

            error_log("[ProdutoModel] Produtos encontrados na busca: " . count($produtos));
            return $produtos;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao buscar produtos pelo termo", $e);
            return [];
        }
    }

    public function editarProduto(int $id): array
    {
        try {
            error_log("[ProdutoModel] Buscando produto para edição. ID: $id");

            $query = "
                SELECT p.id,
                       p.nome,
                       p.descricao,
                       p.preco,
                       p.imagem,
                       p.fornecedor_id,
                       p.categoria,
                       COALESCE(f.nome, 'Fornecedor Desconhecido') AS fornecedor,
                       p.kilograma,
                       p.litro,
                       p.status
                FROM produto p
                LEFT JOIN fornecedor f ON p.fornecedor_id = f.id
                WHERE p.id = :id
                LIMIT 1
            ";

            $produto = $this->projetarEspecifico($query, ['id' => $id]) ?? [];

            $nomeImagem = basename($produto['imagem'] ?? '');
            $caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/" . $nomeImagem;

            $produto['imagem'] = (!empty($nomeImagem) && file_exists($caminhoImagem))
                ? BASE_URL . "app/Assets/image/produtos/" . rawurlencode($nomeImagem)
                : BASE_URL . "app/Assets/image/produtos/produto_default.jpg";

            return $produto;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao editar produto com ID $id", $e);
            return [];
        }
    }

    public function atualizar(array $dados): bool
    {
        $dados = $this->sanitizarDados($dados);
        if (!$dados) {
            error_log("[ProdutoModel] Dados inválidos ao atualizar produto.");
            return false;
        }

        try {
            error_log("[ProdutoModel] Atualizando produto ID: {$dados['id']}");

            $query = "
                UPDATE produto
                SET nome          = :nome,
                    descricao     = :descricao,
                    preco         = :preco,
                    fornecedor_id = :fornecedor_id,
                    kilograma     = :kilograma,
                    litro         = :litro,
                    imagem        = :imagem,
                    categoria     = :categoria,
                    status        = :status
                WHERE id = :id
            ";

            return $this->implementar($query, $dados);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar produto com ID {$dados['id']}", $e);
            return false;
        }
    }

    public function salvarProduto(array $dados): bool
    {
        $dados = $this->sanitizarDados($dados);
        if (!$dados) {
            error_log("[ProdutoModel] Dados inválidos ao salvar novo produto.");
            return false;
        }

        try {
            error_log("[ProdutoModel] Salvando novo produto: " . print_r($dados, true));

            $query = "
                INSERT INTO produto
                    (nome, descricao, preco, fornecedor_id, kilograma, litro, imagem, categoria, status)
                VALUES
                    (:nome, :descricao, :preco, :fornecedor_id, :kilograma, :litro, :imagem, :categoria, :status)
            ";

            return $this->implementar($query, $dados);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao salvar novo produto", $e);
            return false;
        }
    }

    private function sanitizarDados(array $dados): ?array
    {
        if (!isset(
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['fornecedor_id'],
            $dados['kilograma'],
            $dados['litro'],
            $dados['imagem'],
            $dados['categoria']
        )) {
            $_SESSION['msg_erro'][] = "Todos os campos são obrigatórios.";
            error_log("[ProdutoModel] Campos obrigatórios ausentes.");
            return null;
        }

        $dados['status']       = isset($dados['status']) && $dados['status'] === 'ativo' ? 'ativo' : 'inativo';
        $dados['nome']         = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        $dados['descricao']    = htmlspecialchars(trim($dados['descricao']), ENT_QUOTES, 'UTF-8');
        $dados['preco']        = filter_var($dados['preco'], FILTER_VALIDATE_FLOAT);
        $dados['fornecedor_id']= filter_var($dados['fornecedor_id'], FILTER_VALIDATE_INT);
        $dados['kilograma']    = filter_var($dados['kilograma'], FILTER_VALIDATE_FLOAT);
        $dados['litro']        = filter_var($dados['litro'], FILTER_VALIDATE_FLOAT);
        $dados['imagem']       = htmlspecialchars(trim($dados['imagem']), ENT_QUOTES, 'UTF-8');
        $dados['categoria']    = htmlspecialchars(trim($dados['categoria']), ENT_QUOTES, 'UTF-8');

        if ($dados['preco'] === false || $dados['preco'] <= 0) {
            $_SESSION['msg_erro'][] = "O preço deve ser maior que zero.";
            return null;
        }
        if ($dados['kilograma'] === false || $dados['kilograma'] < 0) {
            $_SESSION['msg_erro'][] = "O peso em quilogramas deve ser válido.";
            return null;
        }
        if ($dados['litro'] === false || $dados['litro'] < 0) {
            $_SESSION['msg_erro'][] = "A quantidade em litros deve ser válida.";
            return null;
        }

        return $dados;
    }

    private function registrarErro(string $mensagem, PDOException $e): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("[ProdutoModel] $mensagem: " . $e->getMessage());
        $_SESSION['msg_erro'][] = $mensagem;
    }

    public function buscarPorId(int $id): ?array
    {
        error_log("[ProdutoModel] buscarPorId($id)");
        $produto = $this->editarProduto($id);
        return !empty($produto) ? $produto : null;
    }
}
