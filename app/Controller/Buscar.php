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

    public function index()
    {
        // 🔎 Capturar o termo de busca enviado pelo usuário
        $termo = filter_input(INPUT_GET, 'termo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (empty($termo)) {
            $_SESSION['msg_erro'] = "Por favor, digite um termo para pesquisar.";
            $this->redirecionar("home");
            return;
        }

        try {
            // ✅ Verifica se a função buscarProdutos existe antes de chamar
            if (!method_exists($this->produtoModel, 'buscarProdutos')) {
                $_SESSION['msg_info'] = "A funcionalidade de busca está em desenvolvimento.";
                $this->redirecionar("home");
                return;
            }

            // 🔍 Buscar produtos no banco de dados usando o termo
            $produtos = $this->produtoModel->buscarProdutos($termo);

            // ✅ Se nenhum produto for encontrado, exibe uma mensagem informativa
            if (empty($produtos)) {
                $_SESSION['msg_info'] = "Nenhum resultado encontrado para: " . htmlspecialchars($termo, ENT_QUOTES, 'UTF-8');
            }

            // 📄 Renderizar a view de resultados da busca
            $this->renderizarView('buscar/index', [
                'produtos' => $produtos,
                'termo' => $termo
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao processar a busca.";
            $this->redirecionar("erro");
        }
    }
}
