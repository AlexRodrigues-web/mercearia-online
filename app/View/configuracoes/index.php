<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Garante que o usuário está autenticado antes de exibir a página
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    $_SESSION['msg_erro'] = "Acesso negado! Faça login para continuar.";
    header("Location: " . BASE_URL . "login");
    exit();
}

// ✅ Define a BASE_URL dinamicamente, se necessário
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações | Mercearia</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/css/mercearia.css">
</head>
<body>

    <?php include_once __DIR__ . "/../include/header.php"; ?>

    <div class="container mt-5">
        <h1 class="text-primary text-center">Configurações do Sistema</h1>

        <?php if (!empty($_SESSION['msg_erro'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['msg_erro'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['msg_erro']); ?>
            </div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Bem-vindo, <?= htmlspecialchars($usuarioNome, ENT_QUOTES, 'UTF-8') ?></h5>
                <p class="card-text">Aqui você pode configurar as opções do sistema.</p>

                <a href="<?= BASE_URL ?>configuracoes/editar" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Editar Configurações
                </a>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . "/../include/footer.php"; ?>

</body>
</html>
