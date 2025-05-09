<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\CarrinhoModel;
use Core\ConfigView;

class Carrinho extends Controller
{
    private CarrinhoModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new CarrinhoModel();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['carrinho'] ??= [];
        $_SESSION['desconto'] ??= 0;
    }

    public function index(): void
    {
        $view = new ConfigView("carrinho/index", [
            "itens" => $_SESSION['carrinho'] ?? [],
            "desconto" => $_SESSION['desconto'] ?? 0,
            "cupom_msg" => $_SESSION['cupom_msg'] ?? null
        ]);

        unset($_SESSION['cupom_msg']);
        $view->renderizar();
    }

    public function adicionar(): void
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return;
        }

        $id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        $unidade = filter_input(INPUT_POST, 'unidade', FILTER_SANITIZE_STRING);

        if (!$id || !$quantidade || $quantidade <= 0) {
            $_SESSION['msg_error'] = "Dados inválidos ao adicionar produto.";
            header("Location: " . BASE_URL . "produtos");
            return;
        }

        $produto = $this->model->buscarProdutoPorId($id);

        if ($produto) {
            $preco = $produto['preco'];

            // Verifica promoção válida
            if (isset($produto['tipo'], $produto['desconto'], $produto['fim'])) {
                $agora = time();
                $expira = strtotime($produto['fim']);
                if ($produto['desconto'] > 0 && $expira > $agora) {
                    $tipo = $produto['tipo'];
                    $desc = $produto['desconto'];
                    if ($tipo === 'fixo') {
                        $preco = max(0, $preco - $desc);
                    } elseif ($tipo === 'percentual') {
                        $preco = max(0, $preco * (1 - ($desc / 100)));
                    }
                }
            }

            if (!isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id] = [
                    'id' => $produto['id'],
                    'nome' => $produto['nome'],
                    'preco' => $preco,
                    'quantidade' => $quantidade,
                    'unidade' => $unidade,
                    'imagem' => $produto['imagem']
                ];
            } else {
                $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
            }
        }

        header("Location: " . BASE_URL . "carrinho");
    }

    public function adicionarViaAjax(): void
    {
        if (ob_get_length()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        error_log("[AJAX] Requisição recebida no adicionarViaAjax()");

        $id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
        $unidade = filter_input(INPUT_POST, 'unidade', FILTER_SANITIZE_STRING);

        error_log("[AJAX] Dados recebidos => ID: {$id}, Quantidade: {$quantidade}, Unidade: {$unidade}");

        if (!$id || !$quantidade || $quantidade <= 0) {
            error_log("[AJAX] Dados inválidos recebidos.");
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
            exit;
        }

        try {
            $produto = $this->model->buscarProdutoPorId($id);
            error_log("🔍 [AJAX] Produto encontrado? " . ($produto ? 'SIM' : 'NÃO'));

            if (!$produto) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
                exit;
            }

            $preco = $produto['preco'];

            // Aplica promoção se válida
            if (isset($produto['tipo'], $produto['desconto'], $produto['fim'])) {
                $agora = time();
                $expira = strtotime($produto['fim']);
                if ($produto['desconto'] > 0 && $expira > $agora) {
                    $tipo = $produto['tipo'];
                    $desc = $produto['desconto'];
                    if ($tipo === 'fixo') {
                        $preco = max(0, $preco - $desc);
                    } elseif ($tipo === 'percentual') {
                        $preco = max(0, $preco * (1 - ($desc / 100)));
                    }
                }
            }

            if (!isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id] = [
                    'id' => $produto['id'],
                    'nome' => $produto['nome'],
                    'preco' => $preco,
                    'quantidade' => $quantidade,
                    'unidade' => $unidade,
                    'imagem' => $produto['imagem']
                ];
                error_log("[AJAX] Produto adicionado ao carrinho.");
            } else {
                $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
                error_log("[AJAX] Quantidade atualizada no carrinho.");
            }

            $resposta = ['sucesso' => true];
            error_log("[AJAX] Enviando resposta JSON: " . json_encode($resposta));
            echo json_encode($resposta);
            exit;
        } catch (\Throwable $e) {
            error_log("[AJAX] Exceção capturada: " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno no servidor.']);
            exit;
        }
    }

    public function remover(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }

        header("Location: " . BASE_URL . "carrinho");
    }

    public function atualizar(): void
    {
        if (!isset($_POST['quantidades']) || !is_array($_POST['quantidades'])) {
            header("Location: " . BASE_URL . "carrinho");
            exit;
        }

        foreach ($_POST['quantidades'] as $id => $qtd) {
            $id = (int) $id;
            $qtd = (int) $qtd;

            if (isset($_SESSION['carrinho'][$id]) && $qtd > 0) {
                $_SESSION['carrinho'][$id]['quantidade'] = $qtd;
            }
        }

        header("Location: " . BASE_URL . "carrinho");
    }

    public function aplicarCupom(): void
    {
        $codigo = filter_input(INPUT_POST, 'cupom', FILTER_SANITIZE_STRING);

        if (strtolower($codigo) === "desconto10") {
            $_SESSION['desconto'] = 10;
            $_SESSION['cupom_msg'] = "Cupom válido: 10% de desconto aplicado.";
        } else {
            $_SESSION['desconto'] = 0;
            $_SESSION['cupom_msg'] = "Cupom inválido ou expirado.";
        }

        header("Location: " . BASE_URL . "carrinho");
    }

    public function finalizar(): void
    {
        if (empty($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para finalizar sua compra.";
            header("Location: " . BASE_URL . "login");
            return;
        }

        unset($_SESSION['carrinho'], $_SESSION['desconto']);
        $_SESSION['msg_success'] = "Compra finalizada com sucesso!";
        header("Location: " . BASE_URL . "produtos");
    }

    public function caixa(): void
    {
        $view = new ConfigView("carrinho/caixa", [
            "itens" => $_SESSION['carrinho'] ?? [],
            "desconto" => $_SESSION['desconto'] ?? 0
        ]);

        $view->renderizar();
    }

    public function calcularFrete(): void
    {
        header("Content-Type: application/json");

        $codigoPostal = filter_input(INPUT_POST, 'codigo_postal', FILTER_SANITIZE_STRING);

        if (!$codigoPostal || !preg_match('/^\d{4}-\d{3}$/', $codigoPostal)) {
            echo json_encode([
                'status' => 'erro',
                'mensagem' => 'Código postal inválido. Ex: 1000-001'
            ]);
            return;
        }

        $zona = intval(substr($codigoPostal, 0, 4));
        $frete = ($zona < 2000) ? 2.50 : 4.90;

        echo json_encode([
            'status' => 'ok',
            'valor' => number_format($frete, 2, ',', '.'),
            'prazo' => '2 a 3 dias úteis',
            'mensagem' => "Frete estimado: €{$frete} - entrega em até 3 dias úteis"
        ]);
    }
}
