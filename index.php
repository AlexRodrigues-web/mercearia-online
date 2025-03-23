<?php
ob_start(); // ✅ Inicia o buffer de saída para evitar erros de cabeçalho

// ============================
// 🔒 Configuração da Sessão e Segurança
// ============================
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Alterar para 1 se estiver usando HTTPS
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

// ============================
// 🌍 Definição da BASE_URL (Corrigida Definitivamente)
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Obtém o diretório correto do projeto, evitando que a BASE_URL fique errada
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    
    // Remove o "index.php" do final, se existir
    if (substr($scriptDir, -10) === '/index.php') {
        $scriptDir = substr($scriptDir, 0, -10);
    }

    // Garante que a URL termina com "/"
    define("BASE_URL", rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

// ✅ Definir `ASSETS_URL`
define("ASSETS_URL", BASE_URL . 'app/Assets/');

// ✅ Definir a constante de segurança
if (!defined("MERCEARIA2025")) {
    define("MERCEARIA2025", true);
}

// ✅ Implementação do Timeout da Sessão (30 minutos de inatividade)
if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "login");
    exit();
}
$_SESSION['ultima_atividade'] = time();

// ============================
// 🛠️ Verificação de Arquivos Essenciais
// ============================
$requiredFiles = [
    __DIR__ . "/vendor/autoload.php",
    __DIR__ . "/core/ConfigController.php"
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        error_log("Erro crítico: Arquivo essencial ausente - " . $file);
        die("Erro crítico no sistema. Entre em contato com o suporte.");
    }
}

// ✅ Carrega as dependências
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/core/ConfigController.php";

// ============================
// 🔑 Gerenciamento de Sessão do Usuário (Padronizado)
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = null;
    $_SESSION['usuario_logado'] = false;
    $_SESSION['usuario_nivel'] = 'visitante';
    $_SESSION['usuario_paginas'] = [];
}

// ============================
// 🔄 Middleware de Autenticação (Corrigido para permitir Home)
// ============================
$rotaAtual = $_GET['url'] ?? 'home';
$rotasPublicas = ["home", "produtos", "sobre", "contato", "login", "erro", "paginaPublica"];

if (!in_array($rotaAtual, $rotasPublicas)) {
    if (!$_SESSION['usuario_logado'] || empty($_SESSION['usuario_paginas']) || !is_array($_SESSION['usuario_paginas'])) {
        $_SESSION['msg_erro'] = "Você precisa estar logado para acessar esta página.";
        header("Location: " . BASE_URL . "login");
        exit();
    }
}

// ============================
// 🔄 Chamada do Roteador
// ============================
$rota = new \Core\ConfigController();
$rota->carregar();

// ============================
// ⚠️ Definição de Mensagem de Erro
// ============================
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
    <title>Erro - Mercearia</title>

    <!-- ✅ Links CSS e Fontes -->
    <link rel="stylesheet" href="<?= ASSETS_URL . '/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL . '/css/mercearia.css' ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL . '/css/paginainvalida.css' ?>">
    <link rel="shortcut icon" href="<?= ASSETS_URL . '/image/logo/favicon.ico' ?>">

    <!-- ✅ JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= ASSETS_URL . '/js/bootstrap.bundle.min.js' ?>" defer></script>
    <script src="<?= ASSETS_URL . '/js/eventos.js' ?>" defer></script>
    <script src="<?= ASSETS_URL . '/fontawesome/js/all.min.js' ?>" defer></script>
</head>
<body>
    <!-- ✅ Inclui o cabeçalho -->
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
            <!-- ✅ Inclui o menu lateral -->
            <?php 
            $menuPath = __DIR__ . "/app/View/include/menulateral.php";
            if (file_exists($menuPath)) {
                include_once $menuPath;
            } else {
                error_log("Menu lateral ausente: " . $menuPath);
                echo "<p style='color:red; text-align:center;'>Erro: Menu lateral não encontrado.</p>";
            }
            ?>

            <div class="col-md-9 ml-auto col-lg-10 principal">
                <main role="main">
                    <div class="alertaPagina text-center mt-5">
                        <h1><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></h1>
                        <h5>Verifique se você digitou corretamente o endereço ou tente novamente mais tarde.</h5>
                        <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3">
                            Ir para página inicial
                        </a>
                    </div>    
                </main>
            </div>
        </div>
    </div>
</body>
</html>
