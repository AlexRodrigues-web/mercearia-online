<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use Core\ConfigView;

class Buscar extends Controller
{
    private ProdutoModel $produtoModel;

    public function __construct()
    {
        parent::__construct();
        $this->produtoModel = new ProdutoModel();
    }

    public function index(): void
    {
        // Captura o termo
        $termo = trim(filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        error_log("[Buscar] Termo recebido: " . $termo);

        if (empty($termo)) {
            $_SESSION['msg_erro'] = "Por favor, digite um termo para pesquisar.";
            $this->redirecionar("home");
            return;
        }

        try {
            if (!method_exists($this->produtoModel, 'buscarProdutos')) {
                $_SESSION['msg_info'] = "A funcionalidade de busca está em desenvolvimento.";
                $this->redirecionar("home");
                return;
            }

            $produtos = $this->produtoModel->buscarProdutos($termo);

            if (empty($produtos)) {
                $_SESSION['msg_info'] = "Nenhum resultado encontrado para: <strong>" . htmlspecialchars($termo, ENT_QUOTES, 'UTF-8') . "</strong>";
            }

            $view = new ConfigView("buscar/index", [
                'produtos' => $produtos,
                'termo' => $termo
            ]);

            $view->renderizar();
        } catch (\Throwable $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Ocorreu um erro ao processar sua busca. Tente novamente mais tarde.";
            $this->redirecionar("erro");
        }
    }
}
