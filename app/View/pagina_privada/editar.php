<?php
session_start();

// 🔒 Verificação de autenticação
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// 🌍 BASE_URL dinâmica
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// 📁 Assets
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.bundle.min.js';
$customJsPath = $assetsPath . 'js/customPaginaPrivada.js';

// ✅ Verificação dos arquivos
$arquivosNecessarios = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
foreach ($arquivosNecessarios as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . parse_url($file, PHP_URL_PATH);
    if (!file_exists($path)) {
        error_log("Arquivo ausente: $file");
    }
}

// ✅ Variável de segurança
$pagina = isset($pagina) && is_array($pagina) ? $pagina : ['id' => '', 'nome' => ''];

// 🛡️ CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Página Privada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath) ?>">
    <script src="<?= htmlspecialchars($jsBootstrapPath) ?>" defer></script>
    <script src="<?= htmlspecialchars($customJsPath) ?>" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>admin">Painel Administrativo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>adminproduto">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>adminvenda">Vendas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>adminusuario">Usuários</a></li>
                <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="<?= BASE_URL ?>sair">Sair</a></li>
            </ul>
        </div>
    </div>
</nav> 

    <div class="container py-5">
        <h1 class="text-center text-primary mb-4">Editar Página Privada</h1>

        <!-- 🔔 Mensagens -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg']) ?>
                <?php unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <!-- ✏️ Formulário de Edição -->
        <form action="<?= BASE_URL ?>/pagina_privada/atualizar" method="POST" class="shadow p-4 bg-light rounded">
            <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="mb-3">
                <label for="nome" class="form-label fw-bold">Nome da Página</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($pagina['nome']) ?>" 
                    required 
                    maxlength="30"
                    placeholder="Digite o nome da página">
                <small class="form-text text-muted">Máximo de 30 caracteres.</small>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="<?= BASE_URL ?>/pagina_privada" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
