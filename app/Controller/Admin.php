<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Admin extends Controller
{
    public function __construct()
    {
        // ✅ Inicia a sessão apenas se não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        // ✅ Verifica se o usuário tem permissão para acessar
        if (
            empty($_SESSION['usuario_logado']) || 
            !in_array($_SESSION['usuario_nivel'], ['admin', 'funcionario'])
        ) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários podem acessar esta área.";
            
            // ✅ Certifica-se de que BASE_URL está definida antes de redirecionar
            $baseUrl = defined('BASE_URL') ? BASE_URL : "/";
            header("Location: " . $baseUrl . "erro/403");
            exit();
        }
    }

    public function index(): void
    {
        $dados = [
            'usuario' => $_SESSION['usuario_nome'] ?? 'Usuário',
            'nivel' => $_SESSION['usuario_nivel'] ?? 'Desconhecido',
        ];

        $this->renderizarView("admin/index", $dados);
    }

    private function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }
}
