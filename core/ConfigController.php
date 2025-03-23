<?php
namespace Core;

class ConfigController
{
    private string $url = ''; // Armazena a URL solicitada
    private array $urlDividida = []; // Armazena os segmentos da URL
    private string $urlMetodo = "index"; // Método padrão
    private string $urlController = "Home"; // Agora, a home é a página padrão
    private string $baseURL;
    private ?object $instanciaController = null; // Instância única do controlador

    public function __construct()
    {
        // ✅ Inicia a sessão de forma segura se não estiver ativa
        $this->iniciarSessaoSegura();

        // ✅ Se `BASE_URL` já foi definida no `index.php`, apenas usa
        if (defined('BASE_URL')) {
            $this->baseURL = BASE_URL;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
            $this->baseURL = "{$protocol}://{$host}/{$scriptDir}/";
            define('BASE_URL', $this->baseURL);
        }

        // ✅ Define a constante MERCEARIA2025 para evitar redefinições
        if (!defined("MERCEARIA2025")) {
            define("MERCEARIA2025", true);
        }

        // ✅ Garante que a variável de sessão do usuário seja sempre definida
        if (!isset($_SESSION['usuario_logado'])) {
            $_SESSION['usuario_logado'] = false;
        }

        // ✅ Obtém e sanitiza a URL da requisição
        $this->url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL) ?? '';

        // ✅ Divide a URL para extrair o controlador e método
        if (!empty($this->url)) {
            $this->urlDividida = explode('/', rtrim($this->url, '/'));
            $this->urlController = ucfirst(strtolower($this->urlDividida[0] ?? "home"));
            $this->urlMetodo = strtolower($this->urlDividida[1] ?? "index");
        }

        // ✅ Define páginas públicas e privadas
        $rotasPublicas = ["home", "produtos", "sobre", "contato", "login", "erro", "ajuda", "configuracoes"];
        $rotasRestritas = ["admin", "funcionario", "dashboard", "vendas", "estoque", "nivel", "usuario", "fornecedor", "caixa"];

        // ✅ Permitir acesso à Home sem login
        if (!in_array(strtolower($this->urlController), $rotasPublicas)) {
            if ($_SESSION['usuario_logado'] !== true && in_array(strtolower($this->urlController), $rotasRestritas)) {
                $_SESSION['msg'] = "Acesso restrito! Faça login para continuar.";
                $this->redirecionar("login");
                exit();
            }

            // ✅ Controle de acesso por nível de usuário
            if ($_SESSION['usuario_logado'] === true) {
                $nivel = $_SESSION['usuario_nivel'] ?? 'cliente';

                // 🚫 Bloquear acesso de clientes às áreas administrativas
                if ($nivel === 'cliente' && in_array(strtolower($this->urlController), ["admin", "nivel", "usuario", "fornecedor", "caixa"])) {
                    $_SESSION['msg'] = "Acesso negado! Apenas administradores podem acessar esta área.";
                    $this->redirecionar("home");
                    exit();
                }

                // 🚫 Bloquear acesso de funcionários a certas páginas
                if ($nivel === 'funcionario' && strtolower($this->urlController) === "nivel") {
                    $_SESSION['msg'] = "Acesso negado! Apenas administradores podem acessar esta área.";
                    $this->redirecionar("home");
                    exit();
                }
            }
        }
    }

    public function carregar(): void
    {
        $classe = "\\App\\Controller\\" . $this->urlController;
        $metodo = $this->urlMetodo;

        // ✅ Verifica se a classe existe antes de tentar instanciá-la
        if (!class_exists($classe)) {
            $this->registrarErro("Controlador não encontrado: '{$classe}'");
            $this->redirecionarParaPaginaErro("Página não encontrada!");
            return;
        }

        // ✅ Instancia o controlador corretamente
        $this->instanciaController = new $classe();

        // ✅ Verifica se o método existe antes de chamá-lo
        if (!method_exists($this->instanciaController, $metodo)) {
            $this->registrarErro("Método '{$metodo}' não encontrado no controlador '{$classe}'");
            $this->redirecionarParaPaginaErro("Erro na rota!");
            return;
        }

        try {
            $this->instanciaController->$metodo();
        } catch (\Exception $e) {
            $this->registrarErro("Erro ao executar controlador/método: " . $e->getMessage());
            $this->redirecionarParaPaginaErro("Erro interno. Contate o suporte.");
        }
    }

    private function redirecionarParaPaginaErro(string $mensagem = "Erro desconhecido"): void
    {
        $_SESSION['erro_mensagem'] = $mensagem;
        $this->redirecionar("erro");
    }

    private function registrarErro(string $mensagem): void
    {
        $logDir = __DIR__ . '/../logs';

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            error_log("Erro crítico: Falha ao criar diretório de logs.");
            return;
        }

        $logFile = $logDir . '/error.log';
        file_put_contents($logFile, date("[Y-m-d H:i:s] ") . $mensagem . PHP_EOL, FILE_APPEND);
    }

    private function iniciarSessaoSegura(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionParams = [
                'lifetime' => 0,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'httponly' => true
            ];
            session_set_cookie_params($sessionParams);
            session_start();

            if (!isset($_SESSION['session_regenerated'])) {
                session_regenerate_id(true);
                $_SESSION['session_regenerated'] = true;
            }
        }
    }

    private function redirecionar(string $rota): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $rota = trim($rota, '/');
        $url = $this->baseURL . $rota;

        if (!headers_sent()) {
            header("Location: " . $url);
            exit();
        } else {
            echo "<script>window.location.href='" . addslashes($url) . "';</script>";
            echo "<noscript><meta http-equiv='refresh' content='0;url={$url}'></noscript>";
            exit();
        }
    }
}  
