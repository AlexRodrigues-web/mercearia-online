<?php

namespace App\Model;

use PDOException;

class Estoque extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos(
                "SELECT e.id, e.quantidade, p.nome AS produto_nome
                 FROM estoque e
                 INNER JOIN produto p
                 ON e.produto_id = p.id
                 ORDER BY p.nome"
            );
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar os produtos no estoque: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id do estoque inválido', 1)];

        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT e.id, e.quantidade, p.nome AS produto_nome
                     FROM estoque e
                     INNER JOIN produto p
                     ON e.produto_id = p.id
                     WHERE e.id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar o produto no estoque: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Produto no estoque não encontrado.");
        return [];
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'produto_id', 'quantidade', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id do estoque inválido', 1),
                parent::valida_int($dados['produto_id'], 'produto_id', '*Id do produto inválido', 1),
                parent::valida_int($dados['quantidade'], 'quantidade', '*Quantidade informada inválida', 0)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    parent::implementar(
                        "UPDATE estoque SET produto_id = :produto_id, quantidade = :quantidade WHERE id = :id",
                        ['id' => $dados['id'], 'produto_id' => $dados['produto_id'], 'quantidade' => $dados['quantidade']]
                    );
                    $_SESSION['msg'] = parent::alertaSucesso("Produto no estoque atualizado com sucesso!");
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar o produto no estoque: " . $e->getMessage());
                }
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Preencha corretamente todos os campos e tente novamente.");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Não foi possível atualizar. Verifique os campos e tente novamente.");
        }
    }
}
