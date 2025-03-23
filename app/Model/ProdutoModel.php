<?php

namespace App\Model;

use PDOException;

class ProdutoModel extends Model
{
    /**
     * Lista todos os produtos disponíveis.
     */
    public function listarTodosProdutos(): array
    {
        try {
            $query = "SELECT p.id, p.nome, p.preco, p.imagem,
                             COALESCE(f.nome, 'Fornecedor Desconhecido') AS fornecedor,
                             p.kilograma, p.litro
                      FROM produtos p
                      LEFT JOIN fornecedor f ON p.fornecedor_id = f.id";
            
            $produtos = $this->projetarTodos($query) ?? [];

            // ✅ Ajuste correto do caminho das imagens
            foreach ($produtos as &$produto) {
                $nomeImagem = $produto['imagem'] ?? '';
                $caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/" . $nomeImagem;

                if (!empty($nomeImagem) && file_exists($caminhoImagem)) {
                    $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/" . htmlspecialchars($nomeImagem, ENT_QUOTES, 'UTF-8');
                } else {
                    $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
                }
            }

            return $produtos;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao listar produtos", $e);
            return [];
        }
    }

    /**
     * Obtém um produto para edição.
     */
    public function editarProduto(int $id): array
    {
        try {
            $query = "SELECT p.id, p.nome, p.preco, p.imagem, p.fornecedor_id, 
                             COALESCE(f.nome, 'Fornecedor Desconhecido') AS fornecedor,
                             p.kilograma, p.litro
                      FROM produtos p
                      LEFT JOIN fornecedor f ON p.fornecedor_id = f.id
                      WHERE p.id = :id LIMIT 1";
            
            $produto = $this->projetarEspecifico($query, ['id' => $id]) ?? [];

            // ✅ Se a imagem não existir, usa a imagem padrão
            $nomeImagem = $produto['imagem'] ?? '';
            $caminhoImagem = $_SERVER['DOCUMENT_ROOT'] . "/PROJETO/Mercearia-main/app/Assets/image/produtos/" . $nomeImagem;

            if (!empty($nomeImagem) && file_exists($caminhoImagem)) {
                $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/" . htmlspecialchars($nomeImagem, ENT_QUOTES, 'UTF-8');
            } else {
                $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
            }

            return $produto;
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao editar produto com ID $id", $e);
            return [];
        }
    }

    /**
     * Atualiza um produto existente.
     */
    public function atualizar(array $dados): bool
    {
        $dados = $this->sanitizarDados($dados);
        if (!$dados) {
            return false;
        }

        try {
            $query = "UPDATE produtos 
                      SET nome = :nome, preco = :preco, fornecedor_id = :fornecedor_id, 
                          kilograma = :kilograma, litro = :litro, imagem = :imagem
                      WHERE id = :id";
            return $this->implementar($query, $dados);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao atualizar produto com ID {$dados['id']}", $e);
            return false;
        }
    }

    /**
     * Salva um novo produto no banco de dados.
     */
    public function salvarProduto(array $dados): bool
    {
        $dados = $this->sanitizarDados($dados);
        if (!$dados) {
            return false;
        }

        try {
            $query = "INSERT INTO produtos (nome, preco, fornecedor_id, kilograma, litro, imagem) 
                      VALUES (:nome, :preco, :fornecedor_id, :kilograma, :litro, :imagem)";
            return $this->implementar($query, $dados);
        } catch (PDOException $e) {
            $this->registrarErro("Erro ao salvar novo produto", $e);
            return false;
        }
    }

    /**
     * Sanitiza os dados do produto para evitar injeção de SQL e outros problemas.
     */
    private function sanitizarDados(array $dados): ?array
    {
        if (!isset($dados['nome'], $dados['preco'], $dados['fornecedor_id'], $dados['kilograma'], $dados['litro'], $dados['imagem'])) {
            $_SESSION['msg_erro'][] = "Todos os campos são obrigatórios.";
            return null;
        }

        $dados['nome'] = htmlspecialchars(trim($dados['nome']), ENT_QUOTES, 'UTF-8');
        $dados['preco'] = filter_var($dados['preco'], FILTER_VALIDATE_FLOAT);
        $dados['fornecedor_id'] = filter_var($dados['fornecedor_id'], FILTER_VALIDATE_INT);
        $dados['kilograma'] = filter_var($dados['kilograma'], FILTER_VALIDATE_FLOAT);
        $dados['litro'] = filter_var($dados['litro'], FILTER_VALIDATE_FLOAT);
        $dados['imagem'] = htmlspecialchars(trim($dados['imagem']), ENT_QUOTES, 'UTF-8');

        if (empty($dados['nome'])) {
            $_SESSION['msg_erro'][] = "O nome do produto não pode estar vazio.";
            return null;
        }

        if ($dados['preco'] === false || $dados['preco'] <= 0) {
            $_SESSION['msg_erro'][] = "O preço deve ser um número válido e maior que zero.";
            return null;
        }

        if ($dados['kilograma'] === false || $dados['kilograma'] < 0) {
            $_SESSION['msg_erro'][] = "O peso em quilogramas deve ser um número válido.";
            return null;
        }

        if ($dados['litro'] === false || $dados['litro'] < 0) {
            $_SESSION['msg_erro'][] = "A quantidade em litros deve ser um número válido.";
            return null;
        }

        return $dados;
    }

    /**
     * Registra erros no log e na sessão do usuário.
     */
    private function registrarErro(string $mensagem, PDOException $e): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("$mensagem: " . $e->getMessage());
        $_SESSION['msg_erro'][] = $mensagem;
    }
}
