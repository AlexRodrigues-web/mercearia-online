<?php 
session_start();

// 🔒 Verificar se a constante de segurança está definida
if (!defined("MERCEARIA2025")) {
    http_response_code(403);
    die("Acesso não autorizado.");
}

// 🔎 Obter tipo de erro da URL (se inválido, assume 404)
$errorType = isset($_GET['error']) && is_numeric($_GET['error']) ? $_GET['error'] : '404';

// 🎯 Definir mensagens de erro baseadas no código recebido
$errorMessages = [
    '404' => 'Desculpe! A página solicitada não foi encontrada.',
    '500' => 'Erro Interno do Servidor. Tente novamente mais tarde.',
    '403' => 'Acesso proibido. Você não tem permissão para visualizar esta página.',
    '400' => 'Requisição inválida. Verifique os parâmetros enviados.'
];

$errorMessage = $errorMessages[$errorType] ?? 'Erro desconhecido.';

// 🌍 Definir BASE_URL corretamente
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');

// 📂 Caminho dos assets
$assetsPath = BASE_URL . "app/Assets/";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - Mercearia</title>

    <!-- ✅ Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/paginainvalida.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'fontawesome/css/all.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="shortcut icon" href="<?= htmlspecialchars($assetsPath . 'image/logo/favicon.ico', ENT_QUOTES, 'UTF-8') ?>">

    <!-- ✅ Scripts JS -->
    <script src="<?= htmlspecialchars($assetsPath . 'js/eventos.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <!-- ✅ Inclui o Cabeçalho (com redes sociais) -->
    <?php 
    $headerPath = __DIR__ . "/../include/header.php";
    if (file_exists($headerPath)) {
        include_once $headerPath;
    } else {
        error_log("⚠ Cabeçalho ausente: " . $headerPath);
    }
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- ✅ Inclui o Menu Lateral -->
            <?php 
            $menuPath = __DIR__ . "/../include/menulateral.php";
            if (file_exists($menuPath)) {
                include_once $menuPath;
            } else {
                error_log("⚠ Menu lateral ausente: " . $menuPath);
            }
            ?>

            <div class="col-md-9 ms-auto col-lg-10 principal">
                <main role="main">
                    <!-- 🚨 Exibe Mensagem de Erro -->
                    <div class="alertaPagina text-center mt-5">
                        <h1 class="text-danger"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></h1>
                        <h5>Verifique se você digitou corretamente o endereço ou tente novamente mais tarde.</h5>
                        
                        <!-- 🔄 Botão de Redirecionamento -->
                        <a href="<?= isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] ? BASE_URL . 'home' : BASE_URL . 'login' ?>" 
                           class="btn btn-primary mt-3">
                            Ir para página inicial
                        </a>
                    </div>    
                </main>
            </div>
        </div>
    </div>

    <!-- ✅ Inclui o Rodapé (com redes sociais) -->
    <?php 
    $footerPath = __DIR__ . "/../include/footer.php";
    if (file_exists($footerPath)) {
        include_once $footerPath;
    } else {
        error_log("⚠ Rodapé ausente: " . $footerPath);
    }
    ?>
</body>
</html>
