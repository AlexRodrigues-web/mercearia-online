<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoModel;
use Core\ConfigView;

class Produtos extends Controller
{
    private ProdutoModel $produto;

    public function __construct()
    {
        parent::__construct();
        $this->produto = new ProdutoModel();
    }

    public function index()
    {
        try {
            $produtos = $this->produto->listarTodosProdutos() ?? [];

            // ✅ Corrigindo exibição das imagens
            foreach ($produtos as &$produto) {
                $nomeImagem = $produto['imagem'] ?? '';
                $caminhoImagem = __DIR__ . "/../../Assets/image/produtos/" . $nomeImagem;

                if (!empty($nomeImagem) && file_exists($caminhoImagem)) {
                    $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/" . htmlspecialchars($nomeImagem, ENT_QUOTES, 'UTF-8');
                } else {
                    $produto['imagem'] = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
                }
            }

            if (empty($produtos)) {
                $_SESSION['msg_erro'] = "Nenhum produto encontrado.";
            }

            $view = new ConfigView('produto/index', ['produtos' => $produtos]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao listar produtos.");
        }
    }

    public function adicionar()
    {
        $this->verificarAutenticacao();

        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("produto", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosProduto();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("produto/adicionar", "Dados inválidos.", "erro");
            return;
        }

        try {
            $resultado = $this->produto->adicionarProduto($dados);
            $mensagem = $resultado ? "Produto adicionado com sucesso!" : "Erro ao adicionar produto.";
            $this->redirecionarComMensagem("produto", $mensagem, $resultado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao adicionar produto.");
        }
    }

    public function editar()
    {
        $this->verificarAutenticacao();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirecionarComMensagem("produto", "ID do produto inválido.", "erro");
            return;
        }

        try {
            $produto = $this->produto->buscarPorId($id);
            if (!$produto) {
                $this->redirecionarComMensagem("produto", "Produto não encontrado.", "erro");
                return;
            }

            $view = new ConfigView('produto/editar', ['produto' => $produto]);
            $view->renderizar();
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar produto.");
        }
    }

    public function atualizar()
    {
        $this->verificarAutenticacao();

        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("produto", "Requisição inválida.", "erro");
            return;
        }

        $dados = $this->filtrarDadosProduto();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("produto/editar?id=" . ($dados['id'] ?? ''), "Dados inválidos.", "erro");
            return;
        }

        try {
            $atualizado = $this->produto->editarProduto($dados);
            $mensagem = $atualizado ? "Produto atualizado com sucesso." : "Erro ao atualizar produto.";
            $this->redirecionarComMensagem("produto", $mensagem, $atualizado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar o produto.");
        }
    }

    private function verificarAutenticacao(): void
    {
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar essa funcionalidade.";
            $this->redirecionar("login");
            exit();
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['nome']))) {
            $erros[] = 'O campo "Nome" é obrigatório.';
        }

        if (!isset($dados['preco']) || !is_numeric($dados['preco']) || $dados['preco'] <= 0) {
            $erros[] = 'O campo "Preço" deve ser um número positivo.';
        }

        if (!isset($dados['quantidade']) || !is_numeric($dados['quantidade']) || $dados['quantidade'] < 1) {
            $erros[] = 'O campo "Quantidade" deve ser maior ou igual a 1.';
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function filtrarDadosProduto(): array
    {
        return filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'preco' => FILTER_VALIDATE_FLOAT,
            'quantidade' => FILTER_VALIDATE_INT,
            'descricao' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'imagem' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na ProdutosController: " . $e->getMessage());
        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar('erro');
    }

    protected function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
    }

    protected function redirecionar(string $rota): void
    {
        $rota = trim($rota, '/');
        $urlDestino = BASE_URL . "/" . $rota;

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("Tentativa de redirecionamento para URL inválida: " . $urlDestino);
            $_SESSION['msg_erro'] = "Erro ao redirecionar.";
            exit();
        }

        header("Location: " . $urlDestino);
        exit();
    }

    private function validarCsrfToken(): bool
    {
        $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return isset($_SESSION['csrf_token']) && $csrfToken && $csrfToken === $_SESSION['csrf_token'];
    }
}
