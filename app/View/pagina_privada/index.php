<?php
session_start();

// 🔐 Verificação de Autenticação
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'][] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// 🌍 Definir BASE_URL Dinamicamente
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// 📁 Caminhos dos Assets
$assetsPath = BASE_URL . "/app/Assets/";
$cssBootstrapPath = $assetsPath . 'bootstrap/css/bootstrap.min.css';
$jsBootstrapPath = $assetsPath . 'bootstrap/js/bootstrap.bundle.min.js';
$customJsPath = $assetsPath . 'js/customPaginaPrivada.js';

// 🔍 Verificação de arquivos necessários
$arquivosNecessarios = [$cssBootstrapPath, $jsBootstrapPath];
$arquivosFaltantes = [];
foreach ($arquivosNecessarios as $file) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($file, PHP_URL_PATH);
    if (!file_exists($filePath)) {
        error_log("Arquivo ausente: $file");
        $arquivosFaltantes[] = basename($file);
    }
}

// 🧩 Garantir variável $paginas
$paginas = isset($paginas) && is_array($paginas) ? $paginas : [];

// 🛡️ CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Páginas Privadas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (empty($arquivosFaltantes)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($cssBootstrapPath) ?>">
    <?php else: ?>
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
        <h1 class="text-center text-primary">Gestão de Páginas Privadas</h1>

        <?php if (!empty($_SESSION['msg'])): ?>
            <?php foreach ($_SESSION['msg'] as $msg): ?>
                <div class="alert alert-info text-center mt-3"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <?php if (!empty($arquivosFaltantes)): ?>
            <div class="alert alert-danger mt-4 text-center">
                <strong>Erro:</strong> Arquivos ausentes:
                <ul>
                    <?php foreach ($arquivosFaltantes as $arquivo): ?>
                        <li><?= htmlspecialchars($arquivo) ?></li>
                    <?php endforeach; ?>
                </ul>
                Contate o administrador.
            </div>
        <?php else: ?>
            <script src="<?= htmlspecialchars($jsBootstrapPath) ?>" defer></script>
            <script src="<?= htmlspecialchars($customJsPath) ?>" defer></script>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <h4 class="text-secondary">Páginas Cadastradas</h4>
            <a href="<?= BASE_URL ?>/pagina_privada/cadastrar" class="btn btn-success">Cadastrar Nova Página</a>
        </div>

        <table class="table table-bordered table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($paginas)): ?>
                    <?php foreach ($paginas as $pagina): ?>
                        <tr>
                            <td><?= htmlspecialchars($pagina['id'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($pagina['nome'] ?? 'Sem Título') ?></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/pagina_privada/editar?id=<?= urlencode($pagina['id'] ?? '') ?>" class="btn btn-sm btn-primary">Editar</a>

                                <form action="<?= BASE_URL ?>/pagina_privada/excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir esta página?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($pagina['id'] ?? '') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">Nenhuma página cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
