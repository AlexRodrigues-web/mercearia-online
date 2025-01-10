<?php

namespace App\Model;

class Sair
{
    public function logout(): void
    {
        // Remove todas as variáveis de sessão associadas ao usuário
        unset(
            $_SESSION['usuario_id'],
            $_SESSION['usuario_nome'],
            $_SESSION['usuario_cargo'],
            $_SESSION['usuario_paginas'],
            $_SESSION['paginas_publicas'],
            $_SESSION['form'],
            $_SESSION['Erro_form'],
            $_SESSION['msg'],
            $_SESSION['caixa']
        );

        // Opcional: destrói a sessão completamente
        session_destroy();

        // Redireciona o usuário para a página inicial ou de login
        header("Location: /login");
        exit;
    }
}
