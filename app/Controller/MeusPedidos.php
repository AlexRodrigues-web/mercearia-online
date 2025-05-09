<?php
namespace App\Controller;

use App\Model\PedidoModel;
use Core\ConfigView;

class MeusPedidos extends Controller
{
    private PedidoModel $pedidoModel;

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para ver seus pedidos.";
            $this->redirecionar("login");
            exit();
        }

        $this->pedidoModel = new PedidoModel();
    }

    public function index(): void
    {
        $usuarioId = $_SESSION['usuario_id'];
        $pedidosBrutos = $this->pedidoModel->buscarPedidosPorCliente($usuarioId);

        // Dados para a view
        $pedidosFormatados = [];
        foreach ($pedidosBrutos as $pedido) {
            $pedidosFormatados[] = [
                'numero' => $pedido['id'] ?? 'N/D',
                'data'   => isset($pedido['data_pedido']) ? date('d/m/Y', strtotime($pedido['data_pedido'])) : 'N/D',
                'total'  => $pedido['valor_total'] ?? 0,
                'status' => 'Processando'
            ];
        }

        $view = new ConfigView("meuspedidos/index", ['pedidos' => $pedidosFormatados]);
        $view->renderizar();
    }
}
