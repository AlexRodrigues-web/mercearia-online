<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Garante que a `BASE_URL` está definida
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ✅ Gera token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Caminho dos assets
$assetsPath = BASE_URL . "/app/Assets/";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Mercearia</title>

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="bg-light text-center">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4 bg-white p-4 shadow rounded">
                <h2 class="text-primary">Criar Conta</h2>

                <!-- ✅ Mensagens -->
                <?php if (!empty($_SESSION['msg_erro'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['msg_erro'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['msg_erro']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['msg_sucesso'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['msg_sucesso'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['msg_sucesso']); ?>
                <?php endif; ?>

                <!-- ✅ Formulário de Cadastro -->
                <form action="<?= BASE_URL ?>/registro/cadastrar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" name="nome" id="nome" class="form-control" required placeholder="Digite seu nome">
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="Digite seu e-mail">
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control" required placeholder="Crie uma senha segura">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                </form>

                <p class="mt-3">
                    Já tem uma conta? <a href="<?= BASE_URL ?>/login" class="text-primary">Faça login</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
