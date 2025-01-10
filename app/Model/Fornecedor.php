<?php

namespace App\Model;

use PDOException;

class Fornecedor extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos(
                "SELECT id, nome, cnpj, dt_registro FROM `fornecedor` ORDER BY dt_registro DESC"
            );
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar fornecedores: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT id, nome, cnpj FROM `fornecedor` WHERE id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar fornecedor: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Fornecedor não encontrado.");
        return [];
    }

    public function cadastrar($dados): void
    {
        $this->form_obrigatorio = ['nome', 'cnpj', 'btn_cadastrar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_tamanho($dados['nome'], 'nome', '*Informe um nome, limite 50 caracteres!', 50, 1),
                $this->validaCnpj($dados['cnpj'], 'cnpj', '*Informe um CNPJ válido!')
            ];

            if (parent::formularioValido($this->form_valido)) {
                $dados['id'] = null;
                $dados['dt_registro'] = date('Y-m-d H:i:s');

                try {
                    parent::implementar("INSERT INTO fornecedor (id, nome, cnpj, dt_registro) VALUES (:id, :nome, :cnpj, :dt_registro)", $dados);
                    $_SESSION['msg'] = parent::alertaSucesso("Fornecedor cadastrado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar fornecedor: " . $e->getMessage());
                }
            }
        }

        $_SESSION['form'] = $dados;
        $_SESSION['script'] = "<script>$('#modalCadastrar').modal('show');</script>";
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível cadastrar o fornecedor. Verifique os dados e tente novamente.");
    }

    private function validaCnpj(string $cnpj, string $chave, string $mensagem): bool
    {
        // Adicione aqui a lógica de validação de CNPJ.
        if (!preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', $cnpj)) {
            $_SESSION['Erro_form'][$chave] = $mensagem;
            return false;
        }
        return true;
    }

    public function excluir($id): void
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM `fornecedor` WHERE id = :id", ['id' => $id]);
                $_SESSION['msg'] = parent::alertaSucesso("Fornecedor excluído com sucesso!");
                return;
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir fornecedor: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível excluir o fornecedor. Verifique os dados e tente novamente.");
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'nome', 'cnpj', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id inválido', 1),
                $this->validaCnpj($dados['cnpj'], 'cnpj', '*Informe um CNPJ válido!')
            ];

            if (parent::formularioValido($this->form_valido)) {
                $dados['dt_registro'] = date('Y-m-d H:i:s');

                try {
                    parent::implementar(
                        "UPDATE fornecedor SET nome = :nome, cnpj = :cnpj, dt_registro = :dt_registro WHERE id = :id",
                        $dados
                    );
                    $_SESSION['msg'] = parent::alertaSucesso("Fornecedor atualizado com sucesso!");
                    return;
                } catch (PDOException $e) {
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar fornecedor: " . $e->getMessage());
                }
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Não foi possível atualizar o fornecedor. Verifique os campos e tente novamente.");
    }
}
