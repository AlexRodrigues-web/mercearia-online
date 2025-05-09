<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\EstoqueModel;
use Core\ConfigView;

class Estoque extends Controller
{
    private EstoqueModel $estoqueModel;

    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas funcionários têm permissão.";
            $this->redirecionar("home");
            exit();
        }

        $this->estoqueModel = new EstoqueModel();
    }

    public function index(): void
    {
        try {
            $estoques = $this->estoqueModel->listarTodos() ?? [];
            $view = new ConfigView('estoque/index', ['estoques' => $estoques]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao listar estoque: " . $e->getMessage());
            $_SESSION['msg_erro'] = "Erro ao carregar o estoque.";
            $this->redirecionar("erro");
        }
    }

    public function salvarEntrada(): void
    {
        error_log("salvarEntrada() chamado");
        error_log("Sessão ativa? " . (isset($_SESSION['usuario']) ? 'SIM' : 'NÃO'));
        error_log("Nível: " . ($_SESSION['usuario']['nivel_nome'] ?? 'indefinido'));

        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        $id = $_POST['id'] ?? null;
        $quantidade = $_POST['quantidade'] ?? null;

        if (!$id || !$quantidade || $quantidade <= 0) {
            http_response_code(422);
            $this->respostaJson(false, 'Dados inválidos.');
            return;
        }

        try {
            $resultado = $this->estoqueModel->adicionarEntrada((int)$id, (int)$quantidade);
            $this->respostaJson(
                $resultado,
                $resultado ? 'Entrada registrada com sucesso!' : 'Erro ao atualizar estoque.'
            );
        } catch (\Throwable $e) {
            error_log("Erro em salvarEntrada: " . $e->getMessage());
            http_response_code(500);
            $this->respostaJson(false, 'Erro interno ao registrar entrada.');
        }
    }

    public function salvarEdicao(): void
    {
        error_log("salvarEdicao() chamado");

        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        $id = $_POST['id'] ?? null;
        $preco = $_POST['preco'] ?? null;

        if (!$id || !$preco || !is_numeric($preco)) {
            http_response_code(422);
            $this->respostaJson(false, 'Dados inválidos.');
            return;
        }

        try {
            $resultado = $this->estoqueModel->atualizar([
                'id' => (int)$id,
                'preco' => (float)$preco
            ]);

            $this->respostaJson(
                $resultado,
                $resultado ? 'Produto atualizado com sucesso!' : 'Erro ao salvar edição.'
            );
        } catch (\Throwable $e) {
            error_log("Erro em salvarEdicao: " . $e->getMessage());
            http_response_code(500);
            $this->respostaJson(false, 'Erro interno ao salvar edição.');
        }
    }

    public function entrada(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['msg_erro'] = "ID inválido.";
            $this->redirecionar("estoque");
            return;
        }

        $estoque = $this->estoqueModel->buscarPorId($id);
        if (!$estoque) {
            $_SESSION['msg_erro'] = "Produto não encontrado.";
            $this->redirecionar("estoque");
            return;
        }

        require_once __DIR__ . '/../../app/View/estoque/entrada_modal.php';
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        error_log("[EstoqueController] POST recebido para atualizar");

        $id      = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $preco   = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $unidade = trim(filter_input(INPUT_POST, 'unidade', FILTER_SANITIZE_SPECIAL_CHARS));

        error_log("Dados recebidos para atualizar: id={$id}, preco={$preco}, unidade={$unidade}");

        if (!$id || $preco === false || $unidade === '') {
            $this->respostaJson(false, 'Dados inválidos para atualização.');
            return;
        }

        try {
            $ok = $this->estoqueModel->atualizar([
                'id'      => $id,
                'preco'   => $preco,
                'unidade' => $unidade
            ]);
            $this->respostaJson($ok, $ok ? 'Estoque atualizado com sucesso.' : 'Erro ao atualizar.');
        } catch (\Throwable $e) {
            error_log("Erro ao atualizar estoque: " . $e->getMessage());
            $this->respostaJson(false, 'Erro interno ao atualizar.');
        }
    }

    public function remover(): void
    {
        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->respostaJson(false, 'ID inválido.');
            return;
        }

        $ok = $this->estoqueModel->remover($id);
        $this->respostaJson($ok, $ok ? 'Produto removido do estoque.' : 'Erro ao remover produto.');
    }

    public function importarCsv(): void
    {
        if (!$this->isAjax() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respostaJson(false, 'Requisição inválida.');
            return;
        }

        if (empty($_FILES['csv']['tmp_name'])) {
            $this->respostaJson(false, 'Nenhum arquivo enviado.');
            return;
        }

        $resultado = $this->estoqueModel->importarCSV($_FILES['csv']['tmp_name']);
        $this->respostaJson(true, "Importação concluída.", $resultado);
    }

    public function modalEntrada(): void
    {
        if (!$this->isAjax()) {
            http_response_code(403);
            exit('Acesso negado');
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('ID inválido.');
        }

        try {
            $estoque = $this->estoqueModel->buscarPorId($id);
            if (!$estoque) {
                http_response_code(404);
                exit('Produto não encontrado.');
            }
            require_once __DIR__ . '/../../app/View/estoque/entrada_modal.php';
        } catch (\Throwable $e) {
            error_log("Erro no modalEntrada: " . $e->getMessage());
            http_response_code(500);
            exit('Erro ao carregar modal.');
        }
    }

    public function modalEditar(): void
    {
        if (!$this->isAjax()) {
            http_response_code(403);
            exit('Acesso negado');
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('ID inválido.');
        }

        try {
            $estoque = $this->estoqueModel->buscarPorId($id);
            if (!$estoque) {
                http_response_code(404);
                exit('Produto não encontrado.');
            }
            require_once __DIR__ . '/../../app/View/estoque/editar_modal.php';
        } catch (\Throwable $e) {
            error_log("Erro no modalEditar: " . $e->getMessage());
            http_response_code(500);
            exit('Erro ao carregar modal.');
        }
    }
}
