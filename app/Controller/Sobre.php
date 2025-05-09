<?php 
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;

class Sobre extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_log(">> Entrou no controller SOBRE");

        try {
            $viewPath = "sobre/index";

            $caminhoView = __DIR__ . "/../../View/{$viewPath}.php";

            error_log(">>> VERIFICANDO VIEW EM: " . $caminhoView);

            if (!is_file($caminhoView)) {
                error_log("View NÃO encontrada com is_file: " . $caminhoView);
            } else {
                error_log("View encontrada: " . $caminhoView);
            }

            $view = new ConfigView($viewPath);
            $view->renderizar();
        } catch (\Throwable $e) {
            error_log("Erro ao carregar a página Sobre: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar a página Sobre.";
            $this->redirecionar("erro");
        }
    }
}
