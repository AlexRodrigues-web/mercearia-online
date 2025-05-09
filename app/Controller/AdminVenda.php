<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class AdminVenda extends Controller
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
            $_SESSION['msg_erro'] = "Acesso negado!";
            header("Location: " . BASE_URL . "erro/403");
            exit();
        }
    }

    public function index(): void
    {
        $this->renderizarView("admin/vendas/index", []);
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }
}
