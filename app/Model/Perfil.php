<?php

namespace App\Model;

use PDOException;

class Perfil extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private array $form_valido = [];

    public function listar($id): array
    {
        try {
            return parent::projetarExpecifico(
                "SELECT f.nome, c.nome AS cargo, n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 WHERE f.id = :id LIMIT 1",
                ['id' => $id],
                false
            );
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar perfil: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        try {
            return parent::projetarExpecifico(
                "SELECT id, nome, credencial, senha
                 FROM funcionario
                 WHERE id = :id LIMIT 1",
                ['id' => $id],
                false
            );
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao editar perfil: " . $e->getMessage());
            return [];
        }
    }

    public function atualizar(array $dados): void
    {
        $this->form_obrigatorio = ['credencial', 'senha', 'senhaAtual', 'senhaRepetida', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $dados['id'] = $_SESSION['usuario_id'];
            $this->form_valido = [
                parent::valida_int($dados['id'], 'id', '*Id inválido', 1),
                parent::valida_tamanho($dados['credencial'], 'credencial', '*Preencha corretamente este campo. Deve conter entre 8 e 20 caracteres.', 20, 8),
                parent::valida_tamanho($dados['senhaAtual'], 'senhaAtual', '*Senha inválida!', 64, 8),
                parent::valida_tamanho($dados['senha'], 'senha', '*Informe a nova senha. Deve conter entre 8 e 64 caracteres.', 64, 8),
                parent::valida_tamanho($dados['senhaRepetida'], 'senhaRepetida', '*Repita a nova senha. Deve conter entre 8 e 64 caracteres.', 64, 8)
            ];

            if (parent::formularioValido($this->form_valido)) {
                $senhas = [
                    'senha' => $dados['senha'],
                    'senhaAtual' => $dados['senhaAtual'],
                    'senhaRepetida' => $dados['senhaRepetida']
                ];

                $this->form_valido = [
                    $this->validaCredencial(['credencial' => $dados['credencial']]),
                    $this->validaSenha($senhas)
                ];

                if (parent::formularioValido($this->form_valido)) {
                    try {
                        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
                        unset($dados['btn_atualizar'], $dados['senhaAtual'], $dados['senhaRepetida']);

                        parent::implementar(
                            "UPDATE funcionario
                             SET credencial = :credencial, senha = :senha
                             WHERE id = :id",
                            $dados
                        );

                        $_SESSION['msg'] = parent::alertaSucesso("Dados pessoais atualizados com sucesso!");
                    } catch (PDOException $e) {
                        $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar perfil: " . $e->getMessage());
                    }
                } else {
                    $_SESSION['form'] = $dados;
                    $_SESSION['msg'] = parent::alertaFalha("Erro ao validar credenciais ou senhas. Tente novamente.");
                }
            } else {
                $_SESSION['form'] = $dados;
                $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos corretamente e tente novamente.");
            }
        } else {
            $_SESSION['form'] = $dados;
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos obrigatórios.");
        }
    }

    private function validaCredencial(array $dados): bool
    {
        $id['id'] = $_SESSION['usuario_id'];
        $usuario = parent::projetarExpecifico(
            "SELECT credencial
             FROM funcionario
             WHERE credencial = :credencial AND id != :id LIMIT 1",
            ['credencial' => $dados['credencial'], 'id' => $id['id']]
        );

        if ($usuario) {
            $_SESSION['Erro_form']['credencial'] = 'Essa credencial já existe. Informe outra!';
            return false;
        }

        return true;
    }

    private function validaSenha(array $dados): bool
    {
        $usuario = $this->editar(['id' => $_SESSION['usuario_id']]);
        if (!password_verify($dados['senhaAtual'], $usuario['senha'])) {
            $_SESSION['Erro_form']['senhaAtual'] = 'Senha atual inválida. Tente novamente.';
            return false;
        }

        if ($dados['senha'] !== $dados['senhaRepetida']) {
            $_SESSION['Erro_form']['senhaRepetida'] = 'A nova senha e sua confirmação não coincidem.';
            return false;
        }

        return true;
    }
}
