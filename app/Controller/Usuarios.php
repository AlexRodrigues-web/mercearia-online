<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\UsuarioModel;
use Core\ConfigView;

class Usuarios extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        parent::__construct();
        $this->usuarioModel = new UsuarioModel();

        // 🔒 Verifica se o usuário está logado
        if (empty($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
            $this->redirecionar("login");
            exit();
        }

        // ✅ Apenas administradores podem acessar
        if ($_SESSION['usuario_nivel'] !== 'admin') {
            $_SESSION['msg_erro'] = "Acesso negado! Apenas administradores podem gerenciar usuários.";
            $this->redirecionar("erro/403");
            exit();
        }
    }

    public function index(): void
    {
        try {
            $usuarios = $this->usuarioModel->buscarTodos() ?? [];

            if (empty($usuarios)) {
                $_SESSION['msg_info'] = "Nenhum usuário encontrado.";
            }

            $this->renderizarView("usuario/index", ['usuarios' => $usuarios]);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar a lista de usuários.");
        }
    }

    public function adicionar(): void
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("usuarios", "Requisição inválida!", "erro");
            return;
        }

        $dados = $this->filtrarDadosUsuario();

        if (!$this->validarDados($dados)) {
            $this->redirecionarComMensagem("usuarios/adicionar", "Dados inválidos!", "erro");
            return;
        }

        try {
            // Hash da senha antes de cadastrar
            $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $resultado = $this->usuarioModel->cadastrar($dados);

            $mensagem = $resultado ? "Usuário cadastrado com sucesso!" : "Erro ao cadastrar usuário.";
            $this->redirecionarComMensagem("usuarios", $mensagem, $resultado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao cadastrar usuário.");
        }
    }

    public function editar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirecionarComMensagem("usuarios", "ID do usuário inválido!", "erro");
            return;
        }

        try {
            $usuario = $this->usuarioModel->buscarPorId($id);

            if (!$usuario) {
                $this->redirecionarComMensagem("usuarios", "Usuário não encontrado!", "erro");
                return;
            }

            $this->renderizarView("usuario/editar", ['usuario' => $usuario]);
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao carregar usuário.");
        }
    }

    public function atualizar(): void
    {
        if (!$this->validarCsrfToken()) {
            $this->redirecionarComMensagem("usuarios", "Requisição inválida!", "erro");
            return;
        }

        $dados = $this->filtrarDadosUsuario();

        if (!$this->validarDados($dados, true)) { // Passa true para não exigir senha ao editar
            $this->redirecionarComMensagem("usuarios/editar?id=" . ($dados['id'] ?? ''), "Dados inválidos!", "erro");
            return;
        }

        try {
            // Se a senha não for preenchida, não atualiza esse campo
            if (!empty($dados['senha'])) {
                $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            } else {
                unset($dados['senha']); // Remove do array para evitar sobreposição no banco
            }

            $atualizado = $this->usuarioModel->atualizar($dados);

            $mensagem = $atualizado ? "Usuário atualizado com sucesso!" : "Erro ao atualizar usuário.";
            $this->redirecionarComMensagem("usuarios", $mensagem, $atualizado ? "sucesso" : "erro");
        } catch (\Exception $e) {
            $this->tratarErro($e, "Erro ao atualizar o usuário.");
        }
    }

    private function validarDados(array $dados, bool $edicao = false): bool
    {
        $erros = [];

        if (empty(trim($dados['nome'])) || strlen($dados['nome']) < 3) {
            $erros[] = "O campo 'Nome' é obrigatório e deve ter pelo menos 3 caracteres.";
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "O campo 'E-mail' é inválido.";
        }

        if (!$edicao && (empty($dados['senha']) || strlen($dados['senha']) < 6)) {
            $erros[] = "A senha deve ter pelo menos 6 caracteres.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode('<br>', $erros);
            return false;
        }

        return true;
    }

    private function filtrarDadosUsuario(): array
    {
        return filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'nome' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'senha' => FILTER_DEFAULT,
            'nivel' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ]) ?? [];
    }

    private function tratarErro(\Exception $e, string $mensagemUsuario): void
    {
        error_log("Erro na UsuariosController: " . $e->getMessage());
        $_SESSION['msg_erro'] = $mensagemUsuario;
        $this->redirecionar('erro');
    }

    private function redirecionarComMensagem(string $rota, string $mensagem, string $tipo = 'info'): void
    {
        $_SESSION["msg_{$tipo}"] = $mensagem;
        $this->redirecionar($rota);
    }

    private function redirecionar(string $rota): void
    {
        $rota = trim($rota, '/');
        $urlDestino = BASE_URL . $rota;

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
