<?php
namespace App\Model;

use PDOException;

class Cargo extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome FROM `cargo`");
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar os cargos: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id do cargo inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT id, nome FROM `cargo` WHERE id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar o cargo: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Cargo não encontrado.");
        return [];
    }

    public function cadastrar($dados): void
    {
        $this->form_obrigatorio = ['nome', 'btn_cadastrar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_tamanho($dados['nome'], 'nome', '*Informe um cargo, limite 45 caracteres', 45, 2)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['id'] = null;
                    parent::implementar("INSERT INTO cargo VALUES (:id, :nome)", $dados);
                    $_SESSION['msg'] = parent::alertaSucesso("Cargo cadastrado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar o cargo: " . $e->getMessage());
                }
            }
        }
        $_SESSION['form'] = $dados;
        $_SESSION['script'] = "<script>$('#modalCadastrar').modal('show');</script>";
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível cadastrar o cargo. Verifique os dados e tente novamente.");
    }

    public function excluir($id): void
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id do cargo inválido', 1)];

        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM `cargo` WHERE id = :id", ['id' => $id]);
                $_SESSION['msg'] = parent::alertaSucesso("Cargo excluído com sucesso!");
                return;
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir o cargo: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível excluir o cargo. Verifique os dados e tente novamente.");
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'nome', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id do cargo inválido', 1),
                parent::valida_tamanho($dados['nome'], 'nome', '*Informe o nome do cargo corretamente, limite 45 caracteres', 45, 2)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    parent::implementar(
                        "UPDATE cargo SET nome = :nome WHERE id = :id",
                        ['id' => $dados['id'], 'nome' => $dados['nome']]
                    );
                    $_SESSION['msg'] = parent::alertaSucesso("Cargo atualizado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar o cargo: " . $e->getMessage());
                }
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível atualizar o cargo. Verifique os campos e tente novamente.");
    }
}
