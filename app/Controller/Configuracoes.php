<?php
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Configuracoes extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // ✅ Verifica se o usuário está autenticado
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas administradores podem acessar as configurações
        if ($_SESSION['usuario_nivel'] !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem modificar as configurações.";
            $this->redirecionar("home");
            exit();
        }
    }

    public function index()
    {
        $dados = [
            'titulo' => 'Configurações do Sistema',
            'usuario' => $_SESSION['usuario_nome'] ?? 'Administrador',
        ];

        // ✅ Renderiza a view `configuracoes/index.php`
        $view = new ConfigView("configuracoes/index", $dados);
        $view->renderizar();
    }
}
