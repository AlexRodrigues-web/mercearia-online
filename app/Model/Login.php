<?php

namespace App\Model;

class Login extends Model
{
    private array $obrigatorio = ['credencial', 'senha', 'btnAcessar'];
    private int $quantidade_obrigatorio;
    private ?array $usuario = null;
    private array $credencial = [];
    private bool $formularioValido = false;
    private bool $resultado = false;
    private array $paginasUsuario = [];
    private array $paginasPublicas = [];

    public function login(array $dadosFormulario): void
    {
        $this->credencial['credencial'] = $dadosFormulario['credencial'];
        $this->quantidade_obrigatorio = count($this->obrigatorio);
        $this->formularioValido = parent::existeCamposFormulario($dadosFormulario, $this->obrigatorio, $this->quantidade_obrigatorio);

        if ($this->formularioValido) {
            try {
                $this->usuario = parent::projetarExpecifico(
                    "SELECT f.id, f.nome, c.nome AS cargo, f.credencial, f.senha
                     FROM funcionario f
                     INNER JOIN cargo c ON f.cargo_id = c.id
                     WHERE f.credencial = :credencial LIMIT 1",
                    $this->credencial
                );

                if ($this->usuario) {
                    $this->validaUsuario($dadosFormulario['senha'], $this->usuario['senha']);
                } else {
                    $_SESSION['msg'] = parent::alertaFalha("Credencial ou senha inválida.");
                }
            } catch (\PDOException $e) {
                $_SESSION['msg'] = parent::alertaFalha("Erro ao tentar acessar o sistema: " . $e->getMessage());
            }
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Preencha todos os campos obrigatórios.");
        }
    }

    public function getResultado(): bool
    {
        return $this->resultado;
    }

    private function validaUsuario(string $senha, string $senhaBD): void
    {
        if (password_verify($senha, $senhaBD)) {
            $this->gerarPaginas();
            $this->gerarNovoIdSessao();

            $_SESSION['usuario_id'] = $this->usuario['id'];
            $_SESSION['usuario_nome'] = $this->usuario['nome'];
            $_SESSION['usuario_cargo'] = $this->usuario['cargo'];
            $_SESSION['usuario_paginas'] = $this->paginasUsuario;
            $_SESSION['paginas_publicas'] = $this->paginasPublicas;

            $_SESSION['msg'] = parent::alertaBemvindo("Bem vindo, " . $_SESSION['usuario_nome'] . "!");
            $this->resultado = true;
        } else {
            $_SESSION['msg'] = parent::alertaFalha("Senha inválida.");
        }
    }

    private function gerarPaginas(): void
    {
        $id['id'] = $this->usuario['id'];
        $paginas = new \App\Model\Paginas();
        $this->paginasUsuario = $paginas->acessoPaginas($id);
        $this->paginasPublicas = $paginas->listaPgPublicas();
    }

    private function gerarNovoIdSessao(): void
    {
        session_regenerate_id(true);
    }
}
