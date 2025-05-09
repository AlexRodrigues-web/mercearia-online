<?php
ob_start();

error_log("🚀 Passou pelo index.php");
error_log("📥 URL recebida: " . ($_GET['url'] ?? 'NENHUMA'));

ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if (substr($scriptDir, -10) === '/index.php') {
        $scriptDir = substr($scriptDir, 0, -10);
    }
    define("BASE_URL", rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

define("ASSETS_URL", BASE_URL . 'app/Assets/');

if (!defined("MERCEARIA2025")) {
    define("MERCEARIA2025", true);
}

// 🔐 Sessão expirada: destruir sessão e redirecionar
if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > 1800)) {
    session_unset();
    session_destroy();
    error_log("🔐 Sessão expirada. Redirecionando para login.");
    header("Location: " . BASE_URL . "login");
    exit();
}
$_SESSION['ultima_atividade'] = time();

// 🔐 Garante estrutura básica da sessão
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    $_SESSION['usuario'] = [
        'logado' => false,
        'nivel_nome' => 'visitante',
        'paginas' => []
    ];
}

// 📂 Verifica se arquivos essenciais existem
$requiredFiles = [
    __DIR__ . "/vendor/autoload.php",
    __DIR__ . "/core/ConfigController.php"
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        error_log("❌ Arquivo essencial ausente: " . $file);
        die("Erro crítico no sistema. Entre em contato com o suporte.");
    }
}

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/core/ConfigController.php";

// 🔄 Roteamento
$rotaAtual = $_GET['url'] ?? 'home';
$rotaAtual = strtolower(trim($rotaAtual, "/"));
$rotaAtual = preg_replace('/\/index$/', '', $rotaAtual);

$rotaController = explode('/', $rotaAtual)[0] ?? 'home';

$rotasPublicas = [
    "home", "produtos", "promoções", "promocoes", "promocao", "sobre", "contato", "login", "registro",
    "registro/cadastrar", "login/autenticar", "buscar", "erro", "paginaPublica", "esqueceusenha", "ajuda",
    "carrinho", "carrinho/adicionar", "carrinho/adicionarviaajax", "carrinho/remover", "carrinho/atualizar",
    "carrinho/aplicarCupom", "carrinho/finalizar", "carrinho/limpar", "carrinho/calcularFrete", "carrinho/index", 
    "contato/enviar", "ajuda/enviar", "institucional", "caixa/sucesso", "meuspedidos",
    "configuracoes", "paginas" 
];

$rotaValida = in_array($rotaAtual, $rotasPublicas) || in_array($rotaController, $rotasPublicas);

if (strpos($rotaAtual, 'institucional/index') === 0) {
    $rotaValida = true;
}

if (!$rotaValida) {
    if (
        empty($_SESSION['usuario']['logado']) ||
        !is_array($_SESSION['usuario']['paginas']) ||
        empty($_SESSION['usuario']['paginas'])
    ) {
        $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
        error_log("🔒 Tentativa de acesso à rota restrita '{$rotaAtual}' sem login.");
        header("Location: " . BASE_URL . "login");
        exit();
    }
}

// 🔄 Carrega o roteador principal
try {
    $rota = new \Core\ConfigController();
    $rota->carregar();
} catch (\Throwable $e) {
    error_log("❌ Falha ao carregar ConfigController: " . $e->getMessage());
    // header("Location: " . BASE_URL . "erro/500");
    die("Erro interno. Contate o suporte.");
}

// Mensagens de erro via GET
$errorMessage = "Erro desconhecido.";
if (isset($_GET['error'])) {
    $errorType = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $errorMessages = [
        '404' => 'Desculpe! A página solicitada não foi encontrada.',
        '500' => 'Erro Interno do Servidor. Tente novamente mais tarde.',
        '403' => 'Acesso proibido. Você não tem permissão para visualizar esta página.',
        '400' => 'Requisição inválida. Verifique os parâmetros enviados.'
    ];
    $errorMessage = $errorMessages[$errorType] ?? $errorMessage;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia</title>

    <link rel="stylesheet" href="<?= ASSETS_URL . 'css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL . 'css/mercearia.css' ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL . 'css/mercearia-custom.css' ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL . 'css/paginainvalida.css' ?>">
    <link rel="shortcut icon" href="<?= ASSETS_URL . 'image/logo/favicon.ico' ?>">

    <style>
        body { overflow-x: hidden; }
        main.conteudo-com-menu { margin-left: 16.6%; width: 83.4%; }
        main.conteudo-sem-menu { width: 100%; }
        @media (max-width: 768px) {
            main.conteudo-com-menu { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <?php 
    $headerPath = __DIR__ . "/app/View/include/header.php";
    if (file_exists($headerPath)) {
        include_once $headerPath;
    } else {
        error_log("Cabeçalho ausente: " . $headerPath);
        echo "<p style='color:red; text-align:center;'>Erro: Cabeçalho não encontrado.</p>";
    }
    ?>

    <div class="container-fluid">
        <div class="row">
            <?php
            $temMenu = false;
            if (!empty($_SESSION['usuario']['logado']) && $_SESSION['usuario']['logado'] === true) {
                $menuPath = __DIR__ . "/app/View/include/menulateral.php";
                if (file_exists($menuPath)) {
                    include_once $menuPath;
                    $temMenu = true;
                } else {
                    error_log("Menu lateral ausente: " . $menuPath);
                }
            }
            ?>

            <main class="<?= $temMenu ? 'conteudo-com-menu' : 'conteudo-sem-menu' ?>">
                <?php
                // O conteúdo da página será injetado pelo roteador
                ?>
            </main>
        </div>
    </div>

    <script src="<?= ASSETS_URL . 'js/libs/jquery.min.js' ?>"></script>
    <script src="<?= ASSETS_URL . 'js/libs/bootstrap.bundle.min.js' ?>"></script>
    <script src="<?= ASSETS_URL . 'js/eventos.js' ?>" defer></script>
    <script src="<?= ASSETS_URL . 'fontawesome/js/all.min.js' ?>" defer></script>
</body>
</html>
