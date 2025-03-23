<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\PaginaPublicaModel;
use Core\ConfigView;

class PaginaPublica extends Controller  // ✅ Corrigida a classe base herdada
{
    private PaginaPublicaModel $paginaPublica;
    private array $dados = [];

    public function __construct()
    {
        parent::__construct();
        $this->paginaPublica = new PaginaPublicaModel();
    }

    /**
     * Lista e exibe todas as páginas públicas.
     */
    public function index()
    {
        try {
            $this->dados = $this->paginaPublica->listar() ?? [];

            if (empty($this->dados)) {
                $_SESSION['msg_erro'] = $this->alertaFalha("Nenhuma página pública encontrada.");
            }

            // Renderiza a view com os dados carregados
            $view = new ConfigView('pagina_publica/index', ['paginas' => $this->dados]);
            $view->renderizar();
        } catch (\Exception $e) {
            error_log("Erro ao listar páginas públicas: " . $e->getMessage());
            $_SESSION['msg_erro'] = $this->alertaFalha("Erro ao listar páginas públicas.");
            $this->redirecionar("erro");
        }
    }

    /**
     * Atualiza uma página pública.
     */
    public function atualizar()
    {
        $this->validarCsrfToken();

        // Filtra e sanitiza os dados recebidos
        $this->dados = filter_input_array(INPUT_POST, [
            'id' => FILTER_VALIDATE_INT,
            'titulo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'conteudo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ]) ?? [];

        if (!$this->validarDados($this->dados)) {
            $_SESSION['msg_erro'] = $this->alertaFalha("Dados inválidos ou incompletos.");
            $this->redirecionar("pagina_publica/editar?id=" . ($this->dados['id'] ?? ''));
        }

        try {
            $resultado = $this->paginaPublica->atualizar($this->dados);

            $_SESSION[$resultado ? 'msg_sucesso' : 'msg_erro'] = $resultado
                ? $this->alertaSucesso("Página pública atualizada com sucesso.")
                : $this->alertaFalha("Erro ao atualizar a página pública. Tente novamente.");

            $this->redirecionar("pagina_publica/index");
        } catch (\Exception $e) {
            error_log("Erro ao atualizar página pública: " . $e->getMessage());
            $_SESSION['msg_erro'] = $this->alertaFalha("Erro inesperado ao atualizar a página.");
            $this->redirecionar("erro");
        }
    }

    /**
     * Valida os dados do formulário antes de atualizar.
     *
     * @param array $dados Dados recebidos via POST
     * @return bool Retorna true se os dados forem válidos
     */
    private function validarDados(array $dados): bool
    {
        if (empty($dados['id']) || !is_numeric($dados['id'])) {
            return false;
        }

        if (empty(trim($dados['titulo'])) || empty(trim($dados['conteudo']))) {
            return false;
        }

        return true;
    }

    /**
     * Obtém a URL base do sistema dinamicamente e de forma segura.
     *
     * @return string Retorna a base URL
     */
    private function getBaseUrl(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');

        return "{$protocolo}://{$host}/" . ($scriptDir ? "{$scriptDir}/" : "");
    }

    /**
     * Redireciona o usuário para uma determinada rota.
     *
     * @param string $rota Caminho relativo da rota
     */
    private function redirecionar(string $rota): void
    {
        $urlDestino = $this->getBaseUrl() . trim($rota, '/');

        if (!filter_var($urlDestino, FILTER_VALIDATE_URL)) {
            error_log("Tentativa de redirecionamento para URL inválida: " . $urlDestino);
            $_SESSION['msg_erro'] = $this->alertaFalha("Erro ao redirecionar.");
            exit();
        }

        header("Location: " . $urlDestino);
        exit();
    }

    /**
     * Valida o token CSRF antes de processar requisições sensíveis.
     */
    private function validarCsrfToken(): void
    {
        $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!isset($_SESSION['csrf_token']) || !$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
            $_SESSION['msg_erro'] = $this->alertaFalha("Requisição inválida.");
            error_log("Tentativa de CSRF detectada.");
            $this->gerarCsrfToken();
            $this->redirecionar("pagina_publica");
        }

        unset($_SESSION['csrf_token']);
        $this->gerarCsrfToken();
    }

    /**
     * Garante que o CSRF Token seja gerado e renovado.
     */
    private function gerarCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
