<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Ajuda extends Controller
{
    public function index(): void
    {
        // 🔹 Garante que a sessão está ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔹 Define os dados a serem enviados para a view
        $dados = [
            'titulo' => 'Central de Ajuda',
            'descricao' => 'Encontre respostas para suas dúvidas sobre o sistema.'
        ];

        // 🔹 Renderiza a página de ajuda
        $view = new ConfigView("ajuda/index", $dados);
        $view->renderizar();
    }
}
