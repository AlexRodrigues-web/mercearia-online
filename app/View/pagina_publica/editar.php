<?php
session_start(); // Garante que a sessão foi iniciada

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'][] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Definir dinamicamente o BASE_URL e corrigir inconsistências de caminhos
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/');
    define('BASE_URL', "{$protocol}://{$host}{$basePath}");
}

// Caminhos para os arquivos necessários
$assetsPath = $_SERVER['DOCUMENT_ROOT'] . $basePath . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.min.js';
$customJsPath = $assetsPath . 'js/customPaginaPublica.js';

// Verificar se os arquivos necessários existem
$arquivosNecessarios = [$cssBootstrapPath, $jsBootstrapPath, $customJsPath];
$arquivosFaltantes = [];
foreach ($arquivosNecessarios as $file) {
    if (!file_exists($file)) {
        error_log("Erro: Arquivo ausente - $file");
        $arquivosFaltantes[] = basename($file);
    }
}

// Garantir que $pagina seja um array válido
if (!isset($pagina) || !is_array($pagina)) {
    error_log("Erro: Variável \$pagina não carregada corretamente.");
    $_SESSION['msg'][] = "Erro ao carregar a página. Tente novamente mais tarde.";
    $pagina = ['id' => '', 'nome' => ''];
}

// Inicializar corretamente a variável $_SESSION['msg']
if (!isset($_SESSION['msg']) || !is_array($_SESSION['msg'])) {
    $_SESSION['msg'] = [];
}

// Gerar um token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página Pública</title>

    <!-- Inclusão Condicional de CSS e Fallback -->
    <?php if (empty($arquivosFaltantes)): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/app/Assets/bootstrap/css/bootstrap.min.css">
    <?php else: ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <style>
            body { background-color: #f8d7da; color: #721c24; font-family: Arial, sans-serif; }
            .container { margin-top: 50px; }
            .alert-warning { padding: 15px; border-radius: 5px; font-weight: bold; }
        </style>
    <?php endif; ?>
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
        <h1 class="text-center text-primary mb-4">Editar Página Pública</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <?php foreach ($_SESSION['msg'] as $msg): ?>
                <div class="alert alert-info text-center">
                    <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <!-- Erro se Arquivos Faltam -->
        <?php if (!empty($arquivosFaltantes)): ?>
            <div class="alert alert-danger text-center">
                <strong>Erro:</strong> Os seguintes arquivos estão ausentes:
                <ul>
                    <?php foreach ($arquivosFaltantes as $arquivo): ?>
                        <li><?= htmlspecialchars($arquivo, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
                Bootstrap está sendo carregado via CDN. Contate o administrador do sistema.
            </div>
        <?php else: ?>
            <script src="<?= BASE_URL ?>/app/Assets/bootstrap/js/bootstrap.min.js" defer></script>
            <script src="<?= BASE_URL ?>/app/Assets/js/customPaginaPublica.js" defer></script>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/pagina_publica/atualizar" method="POST" class="shadow p-4 bg-light rounded">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome -->
            <div class="form-group mb-3">
                <label for="nome" class="form-label">Nome da Página</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($pagina['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    maxlength="30" 
                    placeholder="Digite o nome da página"
                    aria-label="Nome da Página">
                <small class="form-text text-muted">Máximo 30 caracteres.</small>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" aria-label="Atualizar Página">Atualizar</button>
                <a href="<?= BASE_URL ?>/pagina_publica" class="btn btn-secondary" aria-label="Cancelar Edição">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
