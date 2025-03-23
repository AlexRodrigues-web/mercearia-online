<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Impede o acesso de usuários não autorizados
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit();
}

// ✅ Apenas funcionários/admins podem acessar
if ($_SESSION['usuario_nivel'] !== 'admin' && $_SESSION['usuario_nivel'] !== 'funcionario') {
    header("Location: " . BASE_URL);
    exit();
}

// ✅ Definir BASE_URL dinamicamente
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

// ✅ Caminhos dos assets
$assetsPath = BASE_URL . "/app/Assets/";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Mercearia</title>

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- ✅ Barra de navegação do Painel Administrativo -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= BASE_URL ?>/admin">Painel Administrativo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/produtos">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/vendas">Vendas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/usuarios">Usuários</a></li>
                <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="<?= BASE_URL ?>/sair">Sair</a></li>
            </ul>
        </div>
    </nav>

    <!-- ✅ Conteúdo do Painel -->
    <div class="container mt-5">
        <h1 class="text-center">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="lead text-center">Este é o painel administrativo da Mercearia.</p>
        
        <div class="row mt-4">
            <!-- ✅ Seção de Produtos -->
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white"><i class="fas fa-box"></i> Produtos</div>
                    <div class="card-body">
                        <p>Gerencie os produtos do estoque.</p>
                        <a href="<?= BASE_URL ?>/admin/produtos" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>

            <!-- ✅ Seção de Vendas -->
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white"><i class="fas fa-shopping-cart"></i> Vendas</div>
                    <div class="card-body">
                        <p>Acompanhe todas as vendas realizadas.</p>
                        <a href="<?= BASE_URL ?>/admin/vendas" class="btn btn-success">Acessar</a>
                    </div>
                </div>
            </div>

            <!-- ✅ Seção de Usuários -->
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark"><i class="fas fa-users"></i> Usuários</div>
                    <div class="card-body">
                        <p>Gerencie os usuários e permissões.</p>
                        <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-warning">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Rodapé -->
    <footer class="text-center mt-5">
        <p>&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</p>
    </footer>

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($assetsPath . 'bootstrap/js/bootstrap.bundle.min.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
