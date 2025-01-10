<?php

namespace App\Model;

use PDOException;

class Pagina_publica extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos("SELECT id, nome, dt_registro FROM pg_publica ORDER BY dt_registro DESC");
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar páginas públicas: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT id, nome, dt_registro FROM pg_publica WHERE id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar a página pública: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Página pública não encontrada.");
        return [];
    }

    public function cadastrar($dados): void
    {
        $this->form_obrigatorio = ['nome', 'btn_cadastrar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_tamanho($dados['nome'], 'nome', '*Preencha este campo, limite 30 caracteres', 30, 1)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['id'] = null;
                    $dados['dt_registro'] = date('Y-m-d H:i:s');

                    parent::implementar(
                        "INSERT INTO pg_publica (id, nome, dt_registro) VALUES (:id, :nome, :dt_registro)",
                        $dados
                    );

                    $_SESSION['msg'] = parent::alertaSucesso("Página pública cadastrada com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar a página pública: " . $e->getMessage());
                }
            }
        }

        $_SESSION['form'] = $dados;
        $_SESSION['script'] = "<script>$('#modalCadastrar').modal('show');</script>";
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível cadastrar a página pública. Verifique os dados e tente novamente.");
    }

    public function excluir($id): void
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM pg_publica WHERE id = :id", ['id' => $id]);
                $_SESSION['msg'] = parent::alertaSucesso("Página pública excluída com sucesso!");
                return;
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir a página pública: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível excluir a página pública. Verifique os dados e tente novamente.");
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'nome', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id inválido', 1),
                parent::valida_tamanho($dados['nome'], 'nome', '*Preencha este campo, limite 30 caracteres', 30, 1)
            ];

            if (parent::formularioValido($this->form_valido)) {
                try {
                    $dados['dt_registro'] = date('Y-m-d H:i:s');

                    parent::implementar(
                        "UPDATE pg_publica SET nome = :nome, dt_registro = :dt_registro WHERE id = :id",
                        ['id' => $dados['id'], 'nome' => $dados['nome'], 'dt_registro' => $dados['dt_registro']]
                    );

                    $_SESSION['msg'] = parent::alertaSucesso("Página pública atualizada com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar a página pública: " . $e->getMessage());
                }
            }
        }

        $_SESSION['msg'] = parent::alertaFalha("Não foi possível atualizar a página pública. Verifique os campos e tente novamente.");
    }
}
