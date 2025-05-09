<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$path}/");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fornecedor</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/mercearia-custom.css">
    <script src="<?= BASE_URL ?>app/Assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Editar Fornecedor</h1>

    <?php if (!empty($_SESSION['msg'])): ?>
        <div class="alert alert-info text-center">
            <?= is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg'] ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>fornecedor/atualizar" method="POST" class="shadow p-4 bg-light rounded">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= $fornecedor['id'] ?? '' ?>">

        <div class="form-group mb-3">
            <label for="nome" class="form-label">Nome do Fornecedor</label>
            <input type="text" name="nome" id="nome" class="form-control" required maxlength="100"
                   value="<?= htmlspecialchars($fornecedor['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group mb-3">
            <label for="nipc" class="form-label">NIPC</label>
            <input type="text" name="nipc" id="nipc" class="form-control" required maxlength="15" pattern="\d{9,15}"
                   value="<?= htmlspecialchars($fornecedor['nipc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <small class="text-muted">Apenas números.</small>
        </div>

        <div class="form-group mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required maxlength="100"
                   value="<?= htmlspecialchars($fornecedor['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="form-control" required maxlength="20"
                   value="<?= htmlspecialchars($fornecedor['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" name="endereco" id="endereco" class="form-control" required maxlength="255"
                   value="<?= htmlspecialchars($fornecedor['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-success px-4">Atualizar</button>
            <a href="<?= BASE_URL ?>fornecedor" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const nipcInput = document.getElementById("nipc");
        if (nipcInput) {
            nipcInput.addEventListener("input", function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>

</body>
</html>
