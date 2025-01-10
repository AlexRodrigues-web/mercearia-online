<?php 
namespace App\Controller;

class Sair extends Controller
{
    public function index()
    {
        // Finaliza a sessão do usuário
        session_destroy();

        // Redireciona para a página de login
        header("Location: /login");
        exit;
    }
}
