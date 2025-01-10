<?php

namespace App\Model;

use PDOException;

class Produtos extends Model
{
    private array $form_obrigatorio = [];
    private array $form_valido = [];
    private int $form_obrigatorio_quantidade = 0;

    public function listar(): array
    {
        try {
            return parent::projetarTodos("
                SELECT p.id, p.nome, p.preco, f.nome AS fornecedor, c.barra AS codigo, e.quantidade
                FROM produto p
                INNER JOIN fornecedor f ON p.fornecedor_id = f.id
                INNER JOIN codigo c ON p.codigo_id = c.id
                INNER JOIN estoque e ON p.id = e.produto_id
            ");
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        try {
            $produto = parent::projetarExpecifico("
                SELECT p.id, p.nome, p.preco, f.id AS fornecedor_id, f.nome AS fornecedor, 
                       c.barra AS codigo, e.quantidade
                FROM produto p
                INNER JOIN fornecedor f ON p.fornecedor_id = f.id
                INNER JOIN codigo c ON p.codigo_id = c.id
                INNER JOIN estoque e ON p.id = e.produto_id
                WHERE p.id = :id LIMIT 1", 
                ['id' => $id],
                false
            );

            if (!$produto) {
                $_SESSION['msg'] = parent::alertaFalha("Produto não encontrado.");
                return [];
            }

            return $produto;
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar o produto: " . $e->getMessage());
            return [];
        }
    }

    public function atualizar(array $dados): void
    {
        $this->form_obrigatorio = [
            'id', 'nome', 'preco', 'fornecedor_id', 'codigo_id', 'litro', 'kilograma', 'quantidade', 'btn_atualizar'
        ];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id inválido!', 1),
                parent::valida_tamanho($dados['nome'], 'nome', '*Preencha este campo, limite 30 caracteres!', 30, 1),
                parent::valida_float($dados['preco'], 'preco', '*Informe o preço corretamente, mínimo R$ 0,10!', 0.10),
                parent::valida_int($dados['fornecedor_id'], 'fornecedor_id', '*Fornecedor inválido!', 1),
                parent::valida_int($dados['codigo_id'], 'codigo_id', '*Código inválido!', 1),
                parent::valida_float($dados['litro'], 'litro', '*Informe o litro corretamente!', 0),
                parent::valida_float($dados['kilograma'], 'kilograma', '*Informe o peso corretamente!', 0),
                parent::valida_int($dados['quantidade'], 'quantidade', '*Quantidade inválida!', 0)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['preco'] = parent::converteFloat($dados['preco']);
                    $dados['litro'] = parent::converteFloat($dados['litro']);
                    $dados['kilograma'] = parent::converteFloat($dados['kilograma']);

                    parent::implementar("
                        UPDATE produto SET 
                        nome = :nome, preco = :preco, fornecedor_id = :fornecedor_id 
                        WHERE id = :id", 
                        $dados
                    );

                    parent::implementar("
                        UPDATE estoque SET quantidade = :quantidade 
                        WHERE produto_id = :id", 
                        ['quantidade' => $dados['quantidade'], 'id' => $dados['id']]
                    );

                    $_SESSION['msg'] = parent::alertaSucesso("Produto atualizado com sucesso!");
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar o produto: " . $e->getMessage());
                }
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Dados inválidos. Verifique e tente novamente.");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos corretamente!");
        }
    }

    public function cadastrar(array $dados): void
    {
        $this->form_obrigatorio = [
            'nome', 'preco', 'fornecedor_id', 'kilograma', 'litro', 'quantidade', 'btn_cadastrar'
        ];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_tamanho($dados['nome'], 'nome', '*Preencha este campo, limite 30 caracteres!', 30, 1),
                parent::valida_float($dados['preco'], 'preco', '*Informe o preço corretamente!', 0.10),
                parent::valida_int($dados['fornecedor_id'], 'fornecedor_id', '*Fornecedor inválido!', 1),
                parent::valida_float($dados['litro'], 'litro', '*Informe o litro corretamente!', 0),
                parent::valida_float($dados['kilograma'], 'kilograma', '*Informe o peso corretamente!', 0),
                parent::valida_int($dados['quantidade'], 'quantidade', '*Quantidade inválida!', 1)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['preco'] = parent::converteFloat($dados['preco']);
                    $dados['litro'] = parent::converteFloat($dados['litro']);
                    $dados['kilograma'] = parent::converteFloat($dados['kilograma']);

                    parent::implementar("
                        INSERT INTO produto (nome, preco, fornecedor_id) 
                        VALUES (:nome, :preco, :fornecedor_id)", 
                        $dados
                    );

                    $_SESSION['msg'] = parent::alertaSucesso("Produto cadastrado com sucesso!");
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar o produto: " . $e->getMessage());
                }
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Dados inválidos. Verifique e tente novamente.");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos corretamente!");
        }
    }

    public function excluir($id): void
    {
        $this->form_valido = [
            parent::valida_int($id, 'id', '*Id inválido!', 1)
        ];

        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM produto WHERE id = :id", ['id' => $id]);
                parent::implementar("DELETE FROM estoque WHERE produto_id = :id", ['id' => $id]);

                $_SESSION['msg'] = parent::alertaSucesso("Produto excluído com sucesso!");
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir o produto: " . $e->getMessage());
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Não foi possível excluir o produto. Verifique os dados e tente novamente.");
        }
    }
}
