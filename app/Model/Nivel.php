<?php

namespace App\Model;

use PDOException;

class Nivel extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome FROM nivel ORDER BY id ASC");
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar níveis: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT id, nome FROM nivel WHERE id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar o nível: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Nível não encontrado.");
        return [];
    }

    public function cadastrar($dados): void
    {
        $this->form_obrigatorio = ['nome', 'btn_cadastrar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_tamanho($dados['nome'], 'nome', '*Informe um nome, limite 7 caracteres', 7, 1)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['id'] = null;
                    parent::implementar("INSERT INTO nivel (id, nome) VALUES (:id, :nome)", $dados);
                    $_SESSION['msg'] = parent::alertaSucesso("Nível cadastrado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar o nível: " . $e->getMessage());
                }
            }
        }

        $_SESSION['form'] = $dados;
        $_SESSION['script'] = "<script>$('#modalCadastrar').modal('show');</script>";
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível cadastrar o nível. Verifique os dados e tente novamente.");
    }

    public function excluir($id): void
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM nivel WHERE id = :id", ['id' => $id]);
                $_SESSION['msg'] = parent::alertaSucesso("Nível excluído com sucesso!");
                return;
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir o nível: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível excluir o nível. Verifique os dados e tente novamente.");
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'nome', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id inválido', 1),
                parent::valida_tamanho($dados['nome'], 'nome', '*Informe um nome, limite 7 caracteres', 7, 1)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    parent::implementar(
                        "UPDATE nivel SET nome = :nome WHERE id = :id",
                        ['id' => $dados['id'], 'nome' => $dados['nome']]
                    );
                    $_SESSION['msg'] = parent::alertaSucesso("Nível atualizado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar o nível: " . $e->getMessage());
                }
            }
        }

        $_SESSION['msg'] = parent::alertaFalha("Não foi possível atualizar o nível. Verifique os campos e tente novamente.");
    }
}
