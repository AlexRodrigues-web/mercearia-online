<?php 
declare(strict_types=1);

namespace App\Controller;

use Core\ConfigView;
use App\Model\UsuarioModel;

class AdminUsuario extends Controller
{
    private string $viewPath;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();

        if (
            empty($_SESSION['usuario']['logado']) ||
            !in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])
        ) {
            $_SESSION['msg_erro'] = "Acesso negado!";
            header("Location: " . BASE_URL . "erro/403");
            exit();
        }

        $this->viewPath = realpath(__DIR__ . '/../View') . '/';
    }

    public function index(): void
    {
        error_log('[AdminUsuario] Acessou método index()');
        $model    = new UsuarioModel();
        $usuarios = $model->buscarTodos();
        error_log('[AdminUsuario] Usuários carregados: ' . count($usuarios));
        $this->renderizarView("admin/usuarios/index", ['usuarios' => $usuarios]);
    }

    public function novo(): void
    {
        error_log('[AdminUsuario] Acessou método novo()');
        $this->carregarModal('admin/usuarios/novo_usuario_modal', []);
    }

    public function editar(): void
    {
        error_log('[AdminUsuario] Acessou método editar()');

        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo "<div class='alert alert-danger'>ID inválido.</div>";
            return;
        }

        $model   = new UsuarioModel();
        $usuario = $model->buscarPorId($id);

        if (!$usuario) {
            echo "<div class='alert alert-warning'>Usuário não encontrado.</div>";
            return;
        }

        $this->carregarModal('admin/usuarios/editar_usuario_modal', ['usuario' => $usuario]);
    }

    public function salvar(): void
    {
        error_log('[AdminUsuario] Acessou método salvar()');

        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        header('Content-Type: application/json; charset=utf-8'); // CABEÇALHO JSON

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método inválido!'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $nome  = trim($_POST['nome']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $nivel = trim($_POST['nivel'] ?? '');

        $model = new UsuarioModel();

        // EDIÇÃO
        if (!empty($_POST['id'])) {
            $id = (int) $_POST['id'];
            if (!$nome || !$email || !$nivel) {
                echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios!'], JSON_UNESCAPED_UNICODE);
                exit();
            }

            $dados = ['id' => $id, 'nome' => $nome, 'email' => $email, 'usuario_nivel' => $nivel];
            $ok = $model->atualizar($dados);

            error_log('[AdminUsuario] Resultado atualização: ' . ($ok ? 'Sucesso' : 'Falha'));

            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Usuário atualizado com sucesso!' : 'Falha ao atualizar usuário.'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // CRIAÇÃO
        $senha = trim($_POST['senha'] ?? '');
        if (!$nome || !$email || !$senha || !$nivel) {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $dados = ['nome' => $nome, 'email' => $email, 'senha' => $senha, 'usuario_nivel' => $nivel];
        $ok = $model->cadastrar($dados);

        error_log('[AdminUsuario] Resultado cadastro: ' . ($ok ? 'Sucesso' : 'Falha'));

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Usuário cadastrado com sucesso!' : 'Falha ao cadastrar usuário.'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function ver(): void
    {
        error_log('[AdminUsuario] Acessou método ver()');

        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo "<div class='alert alert-danger'>ID inválido.</div>";
            return;
        }

        $model   = new UsuarioModel();
        $usuario = $model->buscarPorId($id);

        if (!$usuario) {
            echo "<div class='alert alert-warning'>Usuário não encontrado.</div>";
            return;
        }

        $this->carregarModal('admin/usuarios/ver_usuario_modal', ['usuario' => $usuario]);
    }

    public function bloquear(): void
    {
        error_log('[AdminUsuario] Acessou método bloquear()');

        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json; charset=utf-8'); // CABEÇALHO JSON

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID inválido.'], JSON_UNESCAPED_UNICODE);
                exit();
            }

            $model = new UsuarioModel();
            $ok = $model->excluir($id);

            error_log('[AdminUsuario] Resultado bloqueio: ' . ($ok ? 'Sucesso' : 'Falha'));

            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Usuário bloqueado com sucesso!' : 'Falha ao bloquear usuário.'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo "<div class='alert alert-danger'>ID inválido.</div>";
            return;
        }

        $model   = new UsuarioModel();
        $usuario = $model->buscarPorId($id);

        if (!$usuario) {
            echo "<div class='alert alert-warning'>Usuário não encontrado.</div>";
            return;
        }

        $this->carregarModal('admin/usuarios/bloquear_usuario_modal', ['usuario' => $usuario]);
    }

    public function excluir(): void
{
    error_log('[AdminUsuario] Acessou método excluir()');

    // 1. GET abrir modal via AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo "<div class='alert alert-danger'>ID inválido.</div>";
            return;
        }

        $model = new UsuarioModel();
        $usuario = $model->buscarPorId($id);

        if (!$usuario) {
            echo "<div class='alert alert-warning'>Usuário não encontrado.</div>";
            return;
        }

        error_log("[AdminUsuario] Carregando modal de exclusão para usuário ID {$id}");
        $this->carregarModal('admin/usuarios/excluir_usuario_modal', ['usuario' => $usuario]);
        return;
    }

    // 2. POST processar exclusão
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $confirmar = $_POST['confirmar'] ?? null;

        if (!$id || !$confirmar) {
            $_SESSION['msg_erro'] = 'Requisição inválida.';
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $model = new UsuarioModel();
        $usuario = $model->buscarPorId($id);

        if (!$usuario) {
            $_SESSION['msg_erro'] = 'Usuário não encontrado.';
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $ok = $model->excluir($id);
        error_log("[AdminUsuario] Exclusão do usuário ID {$id}: " . ($ok ? "SUCESSO" : "FALHA"));

        $_SESSION[$ok ? 'msg_sucesso' : 'msg_erro'] = $ok
            ? 'Usuário excluído com sucesso!'
            : 'Erro ao excluir usuário.';

        header("Location: " . BASE_URL . "adminusuario");
    }
}

    protected function renderizarView(string $view, array $dados = []): void
    {
        $configView = new ConfigView($view, $dados);
        $configView->renderizar();
    }

    protected function carregarModal(string $view, array $dados): void
    {
        if (!$this->isAjax()) {
            header("Location: " . BASE_URL . "adminusuario");
            exit();
        }

        $caminho = $this->viewPath . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
        if (!file_exists($caminho)) {
            error_log("Modal {$view} não encontrado! Caminho: {$caminho}");
            echo "<div class='alert alert-danger'>Erro: Modal <strong>{$view}</strong> não encontrado!</div>";
            return;
        }

        extract($dados, EXTR_SKIP);
        require $caminho;
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
