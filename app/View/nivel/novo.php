<?php
// Só inicia sessão se ainda não estiver ativa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificação de autenticação
if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// BASE_URL dinâmica
if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

$assetsPath    = BASE_URL . "/app/Assets/";
$cssBootstrap  = htmlspecialchars($assetsPath . 'css/bootstrap.min.css', ENT_QUOTES);
$jsBootstrap   = htmlspecialchars($assetsPath . 'js/bootstrap.min.js', ENT_QUOTES);
$customJsNivel = htmlspecialchars($assetsPath . 'js/customNivel.js', ENT_QUOTES);

// O token CSRF já foi gerado no controller (método novo())
$csrfToken = $_SESSION['csrf_token'] ?? '';

// valor padrão do status
$status = 'ativo';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Nível</title>
    <link rel="stylesheet" href="<?= $cssBootstrap ?>">
    <script src="<?= $jsBootstrap ?>" defer></script>
    <script src="<?= $customJsNivel ?>" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= BASE_URL ?>/admin">ADM</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">
            <!-- ... itens do menu ... -->
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center text-primary">Cadastrar Novo Nível</h1>

    <div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= BASE_URL ?>/nivel" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
  </div>

    <?php if (!empty($_SESSION['msg']) && is_string($_SESSION['msg'])): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES) ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/nivel/cadastrar" method="POST" class="mt-4 shadow p-4 bg-light rounded">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Nível</label>
            <input
                type="text"
                id="nome"
                name="nome"
                class="form-control"
                placeholder="Ex: admin, cliente"
                required
                maxlength="7"
            >
            <div class="form-text">Máximo 7 caracteres.</div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="ativo"   <?= $status === 'ativo'   ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="<?= BASE_URL ?>/nivel" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<footer class="text-center mt-5 mb-3">
    <p class="text-muted">&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</p>
</footer>

</body>
</html>
