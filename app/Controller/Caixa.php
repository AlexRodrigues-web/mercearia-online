<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use App\Model\PedidoModel;
use Core\ConfigView;

class Caixa extends Controller
{
    private ProdutoModel $produtoModel;
    private PedidoModel $pedidoModel;

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            error_log("[CAIXA] Acesso negado: usuario_id não definido.");
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar o caixa.";
            $this->redirecionar("login");
            exit();
        }

        error_log("[CAIXA] Acesso permitido. usuario_id: " . $_SESSION['usuario_id']);

        $this->produtoModel = new ProdutoModel();
        $this->pedidoModel = new PedidoModel();
    }

    public function index(): void
    {
        error_log("[CAIXA] Entrou no método index()");

        $dados = ['produtos' => [], 'total' => 0];

        if (!empty($_SESSION['carrinho'])) {
            foreach ($_SESSION['carrinho'] as $id => $item) {
                try {
                    $produto = $this->produtoModel->buscarPorId((int)$id);
                    if ($produto) {
                        $produto['quantidade'] = (int) $item['quantidade'];
                        $produto['preco'] = (float) $item['preco'];
                        $produto['subtotal'] = $produto['quantidade'] * $produto['preco'];
                        $dados['produtos'][] = $produto;
                        $dados['total'] += $produto['subtotal'];
                    }
                } catch (\Exception $e) {
                    error_log("[CAIXA] Erro ao carregar produto ID {$id}: " . $e->getMessage());
                }
            }
        }

        if (empty($dados['produtos'])) {
            error_log("[CAIXA] Carrinho vazio");
            $_SESSION['msg_info'] = "Seu carrinho está vazio.";
            $this->redirecionar("produtos");
            return;
        }

        $this->gerarCsrfToken();
        error_log("[CAIXA] Renderizando view do caixa");
        $view = new ConfigView('caixa/index', $dados);
        $view->renderizar();
    }

    public function finalizarPedido(): void
    {
        error_log("[CAIXA] Iniciando processo de finalização do pedido...");
        error_log("[CAIXA] POST recebido: " . print_r($_POST, true));

        if (!$this->validarCsrf()) {
            error_log("[CAIXA] Token CSRF inválido.");
            $this->redirecionarComMensagem("caixa", "Requisição inválida!", "erro");
            exit();
        }

        $dados = filter_input_array(INPUT_POST, [
            'metodo_pagamento' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]);

        if (empty($_SESSION['usuario_id']) || empty($_SESSION['carrinho'])) {
            error_log("[CAIXA] Dados ausentes: usuario_id ou carrinho não definidos.");
            $this->redirecionarComMensagem("caixa", "Dados inválidos para finalizar o pedido.", "erro");
            exit();
        }

        try {
            $pedido = [
                'cliente_id' => $_SESSION['usuario_id'],
                'produto' => $_SESSION['carrinho'],
                'valor_total' => array_reduce($_SESSION['carrinho'], function ($total, $item) {
                    return $total + ($item['preco'] * $item['quantidade']);
                }, 0),
                'metodo_pagamento' => $dados['metodo_pagamento']
            ];

            error_log("[CAIXA] Pedido montado: " . print_r($pedido, true));

            $resultado = $this->pedidoModel->finalizarPedido($pedido);

            if ($resultado) {
                unset($_SESSION['carrinho'], $_SESSION['desconto']);
                $_SESSION['msg_sucesso'] = "Pedido finalizado com sucesso!";

                $_SESSION['pedido_info'] = [
                    'total' => $pedido['valor_total'],
                    'metodo' => $pedido['metodo_pagamento'],
                    'numero' => $resultado['id'] ?? null
                ];

                error_log("[CAIXA] Pedido finalizado com sucesso! Redirecionando para sucesso.");
                $this->redirecionar("caixa/sucesso");
            } else {
                $_SESSION['msg_erro'] = "Erro ao finalizar o pedido.";
                error_log("[CAIXA] Erro ao finalizar o pedido (retorno false).");
                $this->redirecionar("produtos");
            }
        } catch (\Exception $e) {
            error_log("[CAIXA] Erro inesperado: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro inesperado ao processar o pedido.";
            $this->redirecionar("produtos");
        }

        exit();
    }

    public function sucesso(): void
    {
        error_log("[CAIXA] Entrou no método sucesso()");
        error_log(" CAIXA - Exibindo página de sucesso");
        error_log(" Dados do pedido: " . print_r($_SESSION['pedido_info'] ?? [], true));

        $pedidoInfo = $_SESSION['pedido_info'] ?? null;

        if (!$pedidoInfo) {
            error_log("[CAIXA] Nenhum pedido encontrado na sessão.");
            $_SESSION['msg_info'] = "Você ainda não finalizou nenhum pedido.";
            $this->redirecionar("produtos");
            return;
        }

        error_log("[CAIXA] Renderizando página de sucesso");
        $view = new \Core\ConfigView("caixa/sucesso", ['pedido' => $pedidoInfo]);
        $view->renderizar();

        unset($_SESSION['pedido_info']);
    }

    private function validarCsrf(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        error_log("[CAIXA] Validando CSRF token: " . $csrfToken);

        if (!isset($_SESSION['csrf_token']) || !$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = "Token CSRF inválido.";
            $this->gerarCsrfToken();
            return false;
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
        return true;
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("[CAIXA] Novo CSRF token gerado: " . $_SESSION['csrf_token']);
    }

    protected function redirecionar(string $rota): void
    {
        $url = BASE_URL . trim($rota, '/');
        error_log("[CAIXA] Redirecionando para: {$url}");
        header("Location: " . $url);
        exit();
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
    }
}
