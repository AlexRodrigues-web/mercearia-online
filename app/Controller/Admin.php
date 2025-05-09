<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Admin extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        if (
            empty($_SESSION['usuario']['logado']) || 
            !in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])
        ) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários podem acessar esta área.";

            $baseUrl = defined('BASE_URL') ? BASE_URL : "/";
            header("Location: " . $baseUrl . "erro/403");
            exit();
        }
    }

    public function index(): void
    {
        $dados = [
            'usuario' => $_SESSION['usuario']['nome'] ?? 'Usuário',
            'nivel' => $_SESSION['usuario']['nivel_nome'] ?? 'Desconhecido',
        ];

        $this->renderizarView("admin/index", $dados);
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }
}
