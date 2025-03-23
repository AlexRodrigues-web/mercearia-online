<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\PaginaPrivadaModel;
use Core\ConfigController;  // ✅ Importa a classe correta
use Core\ConfigView;

class PaginaPrivada extends Controller
{
    private PaginaPrivada $paginaPrivada;
    private array $dados = [];

    public function __construct()
    {
        parent::__construct();
        $this->paginaPrivada = new PaginaPrivada();
    }

    public function index()
    {
        $this->listar();

        if (!$this->viewExiste('pagina_privada/index')) {
            error_log("Erro: View 'pagina_privada/index' não encontrada.");
            $_SESSION['msg_erro'][] = $this->alertaFalha("Erro ao carregar a página.");
            $this->redirecionar("erro");
        }

        $view = new ConfigView('pagina_privada/index', ['paginas' => $this->dados]);
        $view->renderizar();
    }

    private function listar()
    {
        try {
            $this->dados = $this->paginaPrivada->listar() ?? [];

            if (empty($this->dados)) {
                $_SESSION['msg_erro'][] = $this->alertaFalha("Nenhuma página privada encontrada.");
            }
        } catch (\Exception $e) {
            error_log("Erro ao listar páginas privadas: " . $e->getMessage());
            $_SESSION['msg_erro'][] = $this->alertaFalha("Erro ao carregar as páginas.");
        }
    }

    public function atualizar()
    {
        $this->validarCsrfToken();

        $this->dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'titulo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'conteudo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        if (!$this->validarDados($this->dados)) {
            $_SESSION['msg_erro'][] = $this->alertaFalha("Dados inválidos ou incompletos.");
            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
        }

        try {
            $resultado = $this->paginaPrivada->atualizar($this->dados);
            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'][] = $resultado
                ? $this->alertaSucesso("Página privada atualizada com sucesso.")
                : $this->alertaFalha("Erro ao atualizar a página privada. Tente novamente.");

            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
        } catch (\Exception $e) {
            error_log("Erro ao atualizar página privada: " . $e->getMessage());
            $_SESSION['msg_erro'][] = $this->alertaFalha("Erro inesperado ao atualizar.");
            $this->redirecionar("pagina_privada/editar?id=" . ($this->dados['id'] ?? ''));
        }
    }

    private function validarDados(array $dados): bool
    {
        $erros = [];

        if (empty(trim($dados['titulo'] ?? ''))) {
            $erros[] = "O campo 'Título' é obrigatório.";
        }

        if (empty(trim($dados['conteudo'] ?? ''))) {
            $erros[] = "O campo 'Conteúdo' é obrigatório.";
        }

        if (!empty($erros)) {
            $_SESSION['msg_erro'][] = $this->alertaFalha(implode('<br>', $erros));
            return false;
        }

        return true;
    }

    private function viewExiste(string $view): bool
    {
        $caminhoView = realpath(__DIR__ . "/../../View/" . str_replace('.', '/', $view) . ".php");

        if (!$caminhoView || !file_exists($caminhoView)) {
            error_log("View não encontrada: {$view}");
            return false;
        }

        // Proteção contra Path Traversal
        if (strpos($caminhoView, realpath(__DIR__ . "/../../View")) !== 0) {
            error_log("Tentativa de Path Traversal detectada ao carregar a view: {$view}");
            return false;
        }

        return true;
    }

    private function redirecionar(string $rota): void
    {
        $urlDestino = $this->getBaseUrl() . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("Tentativa de redirecionamento para URL inválida: " . $urlDestino);
            $_SESSION['msg_erro'][] = $this->alertaFalha("Erro ao redirecionar.");
            exit();
        }

        header("Location: {$urlDestino}", true, 302);
        exit();
    }

    private function getBaseUrl(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');

        return "{$protocolo}://{$host}/" . ($scriptDir ? "{$scriptDir}/" : "");
    }

    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'][] = $this->alertaFalha("Requisição inválida.");
            error_log("Tentativa de CSRF detectada.");
            $this->redirecionar("pagina_privada");
        }

        unset($_SESSION['csrf_token']);
    }

    private function gerarCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
