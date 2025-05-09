<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Definir `BASE_URL` somente se ainda não estiver definida
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../core/ConfigController.php'; // 🔹 Inclui a configuração global
    $configController = new \Core\ConfigController();
    define('BASE_URL', $configController->getBaseURL());
}

// ✅ Caminhos dos assets
$assetsPath = BASE_URL . "app/Assets/";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado (403) | Mercearia</title>

    <!-- ✅ Estilos e FontAwesome -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="text-center bg-light">
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="text-center bg-white p-5 shadow rounded">
            <i class="fas fa-exclamation-triangle fa-5x text-danger"></i>
            <h1 class="mt-3 text-danger">Acesso Negado (403)</h1>
            <p class="lead">Você não tem permissão para acessar esta página.</p>
            <p>Se você acredita que isso é um erro, entre em contato com o administrador.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3">
                <i class="fas fa-home"></i> Voltar à Página Inicial
            </a>
        </div>
    </div>

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($assetsPath . 'bootstrap/js/bootstrap.bundle.min.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
