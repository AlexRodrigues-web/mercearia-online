<?php

namespace App\Model;

use PDOException;

class Funcionario extends Model
{
    private array $form_obrigatorio = [];
    private int $form_obrigatorio_quantidade = 0;
    private int $funcionario_id;
    private array $funcionario_paginas = [];
    private array $form_valido = [];

    public function listar(): array
    {
        try {
            return parent::projetarTodos(
                "SELECT f.id, f.nome, f.ativo, c.nome AS cargo, n.nome AS nivel
                 FROM funcionario f
                 INNER JOIN cargo c ON f.cargo_id = c.id
                 INNER JOIN nivel n ON f.nivel_id = n.id
                 ORDER BY f.nome"
            );
        } catch (PDOException $e) {
            $_SESSION['msg'] = parent::alertaFalha("Erro ao listar funcionários: " . $e->getMessage());
            return [];
        }
    }

    public function editar($id): array
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                return parent::projetarExpecifico(
                    "SELECT f.id, f.nome, f.ativo, c.id AS cargo_id, n.id AS nivel_id
                     FROM funcionario f
                     INNER JOIN cargo c ON f.cargo_id = c.id
                     INNER JOIN nivel n ON f.nivel_id = n.id
                     WHERE f.id = :id LIMIT 1",
                    ['id' => $id],
                    false
                );
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao buscar funcionário: " . $e->getMessage());
            }
        }
        $_SESSION['msg'] = parent::alertaFalha("Funcionário não encontrado.");
        return [];
    }

    public function cadastrar($dados): void
    {
        $this->form_obrigatorio = ['nome', 'ativo', 'cargo_id', 'nivel_id', 'credencial', 'senha', 'pg_privada_id', 'btn_cadastrar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $paginas_privadas = [];
            foreach ($dados['pg_privada_id'] as $pagina) {
                array_push($paginas_privadas, parent::valida_int($pagina, 'pg_privada_id', '*Selecione pelo menos uma opção', 0));
            }

            if (parent::formularioValido($paginas_privadas)) {
                $this->form_valido = [
                    parent::valida_tamanho($dados['nome'], 'nome', '*O limite máximo permitido é 70 caracteres', 70, 1),
                    parent::valida_tamanho($dados['credencial'], 'credencial', '*A credencial deve conter entre 8 a 20 caracteres', 20, 8),
                    parent::valida_tamanho($dados['senha'], 'senha', '*A senha deve conter entre 8 a 64 caracteres', 64, 8),
                    parent::valida_bool($dados['ativo'], 'ativo', '*Selecione uma opção'),
                    parent::valida_int($dados['cargo_id'], 'cargo_id', '*Selecione uma opção', 0),
                    parent::valida_int($dados['nivel_id'], 'nivel_id', '*Selecione uma opção', 0)
                ];

                if (parent::formularioValido($this->form_valido)) {
                    try {
                        $this->conn->beginTransaction();
                        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
                        $dados['dt_registro'] = date('Y-m-d H:i:s');
                        $dados['id'] = null;

                        parent::implementar(
                            "INSERT INTO funcionario (id, nome, ativo, cargo_id, nivel_id, credencial, senha, dt_registro) 
                             VALUES (:id, :nome, :ativo, :cargo_id, :nivel_id, :credencial, :senha, :dt_registro)",
                            $dados
                        );

                        $this->funcionario_id = intval($this->conn->lastInsertId());
                        foreach ($dados['pg_privada_id'] as $pagina) {
                            parent::implementar(
                                "INSERT INTO funcionario_pg_privada (funcionario_id, pg_privada_id, dt_registro) 
                                 VALUES (:funcionario_id, :pg_privada_id, :dt_registro)",
                                [
                                    'funcionario_id' => $this->funcionario_id,
                                    'pg_privada_id' => $pagina,
                                    'dt_registro' => $dados['dt_registro']
                                ]
                            );
                        }

                        $this->conn->commit();
                        $_SESSION['msg'] = parent::alertaSucesso("Funcionário cadastrado com sucesso!");
                    } catch (PDOException $e) {
                        $this->conn->rollBack();
                        $_SESSION['msg'] = parent::alertaFalha("Erro ao cadastrar funcionário: " . $e->getMessage());
                    }
                }
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Preencha todas as páginas de acesso corretamente.");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos corretamente e tente novamente.");
        }
    }

    public function excluir($id): void
    {
        $this->form_valido = [parent::valida_int($id, 'id', '*Id inválido', 1)];
        if (parent::formularioValido($this->form_valido)) {
            try {
                parent::implementar("DELETE FROM funcionario WHERE id = :id", ['id' => $id]);
                $_SESSION['msg'] = parent::alertaSucesso("Funcionário excluído com sucesso!");
            } catch (PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao excluir funcionário: " . $e->getMessage());
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Funcionário não encontrado.");
        }
    }

    public function atualizar($dados): void
    {
        $this->form_obrigatorio = ['id', 'nome', 'cargo_id', 'nivel_id', 'ativo', 'pg_privada_id', 'btn_atualizar'];
        $this->form_obrigatorio_quantidade = count($this->form_obrigatorio);

        if (parent::existeCamposFormulario($dados, $this->form_obrigatorio, $this->form_obrigatorio_quantidade)) {
            $paginas_privadas = [];
            foreach ($dados['pg_privada_id'] as $pagina) {
                array_push($paginas_privadas, parent::valida_int($pagina, 'pg_privada_id', '*Selecione pelo menos uma opção', 0));
            }

            if (parent::formularioValido($paginas_privadas)) {
                $this->form_valido = [
                    parent::valida_int($dados['id'], 'id', '*Id inválido', 1),
                    parent::valida_tamanho($dados['nome'], 'nome', '*O limite máximo permitido é 70 caracteres', 70, 1),
                    parent::valida_bool($dados['ativo'], 'ativo', '*Selecione uma opção'),
                    parent::valida_int($dados['cargo_id'], 'cargo_id', '*Selecione uma opção', 0),
                    parent::valida_int($dados['nivel_id'], 'nivel_id', '*Selecione uma opção', 0)
                ];

                if (parent::formularioValido($this->form_valido)) {
                    try {
                        $this->conn->beginTransaction();
                        parent::implementar(
                            "UPDATE funcionario SET nome = :nome, ativo = :ativo, cargo_id = :cargo_id, nivel_id = :nivel_id 
                             WHERE id = :id",
                            $dados
                        );

                        parent::implementar(
                            "DELETE FROM funcionario_pg_privada WHERE funcionario_id = :funcionario_id",
                            ['funcionario_id' => $dados['id']]
                        );

                        foreach ($dados['pg_privada_id'] as $pagina) {
                            parent::implementar(
                                "INSERT INTO funcionario_pg_privada (funcionario_id, pg_privada_id, dt_registro) 
                                 VALUES (:funcionario_id, :pg_privada_id, :dt_registro)",
                                [
                                    'funcionario_id' => $dados['id'],
                                    'pg_privada_id' => $pagina,
                                    'dt_registro' => date('Y-m-d H:i:s')
                                ]
                            );
                        }

                        $this->conn->commit();
                        $_SESSION['msg'] = parent::alertaSucesso("Funcionário atualizado com sucesso!");
                    } catch (PDOException $e) {
                        $this->conn->rollBack();
                        $_SESSION['msg'] = parent::alertaFalha("Erro ao atualizar funcionário: " . $e->getMessage());
                    }
                }
            } else {
                $_SESSION['msg'] = parent::alertaFalha("Preencha todas as páginas de acesso corretamente.");
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos corretamente e tente novamente.");
        }
    }
}
