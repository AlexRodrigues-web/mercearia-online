<?php
namespace Core;

class ConfigController
{
    private string $url = '';
    private array $urlDividida = [];
    private string $urlMetodo = "index";
    private string $urlController = "Home";
    private string $baseURL;
    private ?object $instanciaController = null;

    public function __construct()
    {
        $this->iniciarSessaoSegura();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("POST recebido no ConfigController: " . print_r($_POST, true));
        }

        if (defined('BASE_URL')) {
            $this->baseURL = BASE_URL;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
            $this->baseURL = "{$protocol}://{$host}/{$scriptDir}/";
            define('BASE_URL', $this->baseURL);
        }

        if (!defined("MERCEARIA2025")) {
            define("MERCEARIA2025", true);
        }

        $this->url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL) ?? '';

        if (!empty($this->url)) {
            $this->urlDividida = explode('/', rtrim($this->url, '/'));
            $this->urlController = strtolower($this->urlDividida[0] ?? "home");
            $this->urlMetodo = strtolower($this->urlDividida[1] ?? "index");
        }

        // Correções de nome de controller baseadas em rota
        $correcoes = [
            'promocoes' => 'Promocao',
            'produto' => 'Produtos',
            'usuario' => 'Usuarios',
            'venda' => 'Vendas',
            'fornecedor' => 'Fornecedor',
            'funcionario' => 'Funcionario',
            'estoque' => 'Estoque',
            'nivel' => 'Nivel',
            'pagina_privada' => 'PaginaPrivada',
            'pagina_publica' => 'PaginaPublica',
            'relatorios' => 'Relatorios',         // Já adicionado
            'configuracoes' => 'Configuracoes',   // Novo
            'paginas' => 'Paginas',               // Novo
            'buscar' => 'Buscar'                  // Correção adicionada
        ];
        
        $ctrlLower = strtolower($this->urlController);
        if (array_key_exists($ctrlLower, $correcoes)) {
            error_log("Correção de rota aplicada: '{$ctrlLower}' -> '{$correcoes[$ctrlLower]}'");
            $this->urlController = $correcoes[$ctrlLower];
        } elseif (!empty($this->urlController)) {
            $this->urlController = ucfirst($this->urlController);
        }

        $rotasPublicas = [
            "home", "produtos", "sobre", "contato", "login", "registro",
            "registro/cadastrar", "login/autenticar", "buscar", "erro", "paginaPublica", "esqueceusenha", "ajuda",
            "carrinho", "carrinho/adicionar", "carrinho/adicionarviaajax", "carrinho/remover", "carrinho/atualizar",
            "carrinho/aplicarCupom", "carrinho/finalizar", "institucional", "meuspedidos", "contato/enviar",
            "ajuda/enviar", "promocoes", "promocao"
        ];

        $rotaCompleta = strtolower($this->urlController . "/" . $this->urlMetodo);

        if (strpos($rotaCompleta, 'institucional/index') === 0) {
            return;
        }

        $rotasRestritas = [
            "admin", "adminproduto", "adminvenda", "adminusuario",
            "funcionario", "dashboard", "vendas", "estoque", "nivel", "usuarios", "fornecedor", "caixa",
            "pagina_privada"
        ];

        if (!in_array(strtolower($this->urlController), $rotasPublicas) && !in_array($rotaCompleta, $rotasPublicas)) {
            if ((!isset($_SESSION['usuario']) || $_SESSION['usuario']['logado'] !== true) && in_array(strtolower($this->urlController), $rotasRestritas)) {
                $_SESSION['msg'] = "Acesso restrito! Faça login para continuar.";
                error_log("Acesso negado: usuário não logado tentando acessar '{$this->urlController}'");
                $this->redirecionar("login");
                exit();
            }

            if (isset($_SESSION['usuario']) && $_SESSION['usuario']['logado'] === true) {
                $nivel = $_SESSION['usuario']['nivel_nome'] ?? 'cliente';

                if ($nivel === 'cliente' && in_array(strtolower($this->urlController), ["admin", "nivel", "usuarios", "fornecedor", "funcionario", "estoque"])) {
                    $_SESSION['msg'] = "Acesso negado! Apenas administradores podem acessar esta área.";
                    error_log("Acesso negado para cliente em rota '{$this->urlController}'");
                    $this->redirecionar("home");
                    exit();
                }

                if ($nivel === 'funcionario' && in_array(strtolower($this->urlController), ["nivel"])) {
                    $_SESSION['msg'] = "Acesso negado! Apenas administradores podem acessar esta área.";
                    error_log("Acesso negado para funcionário em rota '{$this->urlController}'");
                    $this->redirecionar("home");
                    exit();
                }
            }
        }
    }

    public function carregar(): void
    {
        error_log(">> ConfigController: tentando carregar \\App\\Controller\\{$this->urlController}::{$this->urlMetodo}()");

        $classe = "\\App\\Controller\\" . ucfirst($this->urlController);
        $metodo = $this->urlMetodo;

        if (!class_exists($classe)) {
            $this->registrarErro("Controlador não encontrado: '{$classe}'");
            $this->redirecionarParaPaginaErro("Página não encontrada!");
            return;
        }

        $this->instanciaController = new $classe();

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
        exit();
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
