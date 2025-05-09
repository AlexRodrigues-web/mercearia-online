<?php

namespace App\Controller;

use App\Model\PromocaoModel;

class Promocao extends Controller
{
    private PromocaoModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new PromocaoModel();
    }

    public function index(): void
    {
        error_log("[PromocaoController] Método index() iniciado.");

        try {
            $produtosPromocao = $this->model->buscarProdutosPromocionaisAleatorios(5);

            if (empty($produtosPromocao)) {
                error_log("Nenhuma promoção ativa encontrada.");
            } else {
                error_log("Promoções carregadas: " . count($produtosPromocao));
            }

            $this->renderizarView("promocao/index", [
                'titulo' => 'Ofertas e Promoções',
                'produtos' => $produtosPromocao
            ]);
        } catch (\Throwable $e) {
            error_log("Erro ao carregar promoções: " . $e->getMessage());
            $this->redirecionarComMensagem('erro', 'Erro ao carregar as promoções.', 'erro');
        }
    }
}
