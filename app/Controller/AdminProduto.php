<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\ProdutoAdminModel;
use Core\ConfigView;

class AdminProduto extends Controller
{
    private ProdutoAdminModel $produto;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        parent::__construct();

        if (
            empty($_SESSION['usuario']['logado']) ||
            !in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'], true)
        ) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores ou funcionários.";
            header("Location: " . BASE_URL . "login");
            exit();
        }

        $this->produto = new ProdutoAdminModel();
    }

    public function index(): void
    {
        $produtos = $this->produto->listarTodosProdutos() ?? [];
        $this->renderizarView("admin/produtos/index", ['produtos' => $produtos]);
    }

    public function novo(): void
    {
        $fornecedores = $this->produto->listarFornecedores();
        $this->renderizarView("adminproduto/novo", ['fornecedores' => $fornecedores]);
    }

    public function salvar(): void
    {
        $dados = $this->filtrarDadosProduto();
        $dados['imagem'] = $this->processarUploadImagem();
        $dados['dt_registro'] = date('Y-m-d H:i:s');

        error_log("[adminproduto/salvar] Dados recebidos: " . json_encode($dados));

        if (!$this->validarDados($dados)) {
            $this->redirecionar("adminproduto/novo");
            return;
        }

        $resultado = $this->produto->salvarProduto($dados);
        $_SESSION["msg_" . ($resultado ? 'sucesso' : 'erro')] = $resultado
            ? "Produto cadastrado com sucesso!"
            : "Erro ao cadastrar produto.";

        $this->redirecionar("adminproduto");
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("adminproduto");
            return;
        }

        $produto = $this->produto->buscarPorId($id);
        $fornecedores = $this->produto->listarFornecedores();

        if (!$produto) {
            $_SESSION['msg_erro'] = "Produto não encontrado.";
            $this->redirecionar("adminproduto");
            return;
        }

        error_log("[adminproduto/editar] Carregando produto ID: {$id}");
        $this->renderizarView("adminproduto/editar", ['produto' => $produto, 'fornecedores' => $fornecedores]);
    }

    public function atualizar(): void
    {
        $dados = $this->filtrarDadosProduto();
        $novaImagem = $this->processarUploadImagem();
        if ($novaImagem) $dados['imagem'] = $novaImagem;

        error_log("[adminproduto/atualizar] Dados recebidos: " . json_encode($dados));

        if (!$this->validarDados($dados, true)) {
            $this->redirecionar("adminproduto/editar?id=" . ($dados['id'] ?? ''));
            return;
        }

        $resultado = $this->produto->editarProduto($dados);
        $_SESSION["msg_" . ($resultado ? 'sucesso' : 'erro')] = $resultado
            ? "Produto atualizado com sucesso!"
            : "Erro ao atualizar produto.";

        $this->redirecionar("adminproduto");
    }

    public function excluir(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("adminproduto");
            return;
        }

        $resultado = $this->produto->excluirProduto($id);
        $_SESSION["msg_" . ($resultado ? 'sucesso' : 'erro')] = $resultado
            ? "Produto excluído com sucesso."
            : "Erro ao excluir o produto.";

        $this->redirecionar("adminproduto");
    }

    public function exportar(): void
    {
        try {
            $produtos = $this->produto->listarTodosProdutos();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=produtos_exportados.csv');

            $saida = fopen('php://output', 'w');

            fputcsv($saida, [
                'ID', 'Nome', 'Descrição', 'Preço', 'Estoque', 'Categoria', 'Unidade', 'SKU',
                'Validade', 'Estoque Mínimo', 'Local', 'Fornecedor ID', 'Custo', 'NIPC', 'Status'
            ]);

            foreach ($produtos as $produto) {
                fputcsv($saida, [
                    $produto['id'],
                    $produto['nome'],
                    $produto['descricao'],
                    $produto['preco'],
                    $produto['estoque'],
                    $produto['categoria'],
                    $produto['unidade'],
                    $produto['sku'],
                    $produto['validade'],
                    $produto['estoque_minimo'],
                    $produto['local'],
                    $produto['fornecedor_id'],
                    $produto['custo'],
                    $produto['nipc'],
                    $produto['status']
                ]);
            }

            fclose($saida);
            exit;

        } catch (\Exception $e) {
            error_log("Erro ao exportar produtos: " . $e->getMessage());
            $this->redirecionar('erro');
        }
    }

    private function filtrarDadosProduto(): array
    {
        return [
            'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
            'nome' => trim((string)(filter_input(INPUT_POST, 'nome') ?? '')),
            'descricao' => trim((string)(filter_input(INPUT_POST, 'descricao') ?? '')),
            'preco' => (float)(filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT) ?? 0),
            'quantidade' => (int)(filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT) ?? 0),
            'categoria' => trim((string)(filter_input(INPUT_POST, 'categoria') ?? '')),
            'unidade' => trim((string)(filter_input(INPUT_POST, 'unidade') ?? '')),
            'sku' => trim((string)(filter_input(INPUT_POST, 'sku') ?? '')),
            'validade' => trim((string)(filter_input(INPUT_POST, 'validade') ?? '')) ?: null,
            'estoque_minimo' => (int)(filter_input(INPUT_POST, 'estoque_minimo', FILTER_VALIDATE_INT) ?? 0),
            'local' => trim((string)(filter_input(INPUT_POST, 'local') ?? '')),
            'fornecedor_id' => (int)(filter_input(INPUT_POST, 'fornecedor_id', FILTER_VALIDATE_INT) ?? 0),
            'custo' => (float)(filter_input(INPUT_POST, 'custo', FILTER_VALIDATE_FLOAT) ?? 0),
            'nipc' => trim((string)(filter_input(INPUT_POST, 'nipc') ?? '')),
            'status' => isset($_POST['status']) && $_POST['status'] === 'ativo' ? 'ativo' : 'inativo',
        ];
    }

    private function validarDados(array $dados, bool $edicao = false): bool
    {
        $erros = [];

        if (empty($dados['nome'])) $erros[] = 'Nome obrigatório.';
        if ($dados['preco'] <= 0) $erros[] = 'Preço inválido.';
        if ($dados['quantidade'] < 0) $erros[] = 'Quantidade inválida.';
        if (empty($dados['categoria'])) $erros[] = 'Categoria obrigatória.';
        if ($dados['estoque_minimo'] < 0) $erros[] = 'Estoque mínimo inválido.';

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode("<br>", $erros);
            error_log("[adminproduto/validarDados] Erros: " . implode(" | ", $erros));
            return false;
        }

        return true;
    }

    private function processarUploadImagem(): ?string
    {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeImagem = uniqid('produto_', true) . '.' . strtolower($ext);
            $caminho = __DIR__ . "/../Assets/image/produtos/" . $nomeImagem;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
                return $nomeImagem;
            }

            $_SESSION['msg_erro'] = "Erro ao fazer upload da imagem.";
            error_log("[adminproduto/processarUploadImagem] Falha ao mover imagem.");
        }

        return null;
    }

    protected function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }

    protected function redirecionar(string $rota): void
    {
        header("Location: " . BASE_URL . "/" . trim($rota, '/'));
        exit();
    }
}
