<?php 
// ============================
// 🔒 Verificar Definições de Segurança
// ============================
if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado. Contate o administrador.");
}

// ============================
// 📂 Definir `ASSETS_URL` corretamente se não estiver definida
// ============================
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim(BASE_URL, '/') . '/app/Assets');
}

// ============================
// 🖼️ Caminho Correto do Logo
// ============================
$logoPath = ASSETS_URL . '/image/logo/logo.jpg';
$defaultLogoPath = ASSETS_URL . '/image/logo/default-logo.jpg';

// Verifica se o logo existe antes de exibi-lo
$logoFullPath = $_SERVER['DOCUMENT_ROOT'] . '/app/Assets/image/logo/logo.jpg';
if (!file_exists($logoFullPath)) {
    error_log("Aviso: Logo da empresa não encontrado, usando logo padrão.");
    $logoPath = $defaultLogoPath;
}

// ============================
// 🔑 Verificar Status do Usuário (Logado ou Não)
// ============================
$usuarioLogado = isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Visitante';
$usuarioNivel = $_SESSION['usuario_nivel'] ?? 'comum';

// ✅ Determinar se a página é a Home
$paginaAtual = strtolower($_SERVER['REQUEST_URI']);
$ehHome = (strpos($paginaAtual, "home") !== false || $paginaAtual === BASE_URL);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia - Bem-vindo</title>

    <!-- ✅ Inclusão do jQuery antes do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- ✅ Bootstrap CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/bootstrap/css/bootstrap.min.css">
    
    <!-- ✅ FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- ✅ Garantia do Carregamento do jQuery antes do Bootstrap -->
    <script>
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        }
    </script>

    <!-- ✅ Bootstrap JavaScript -->
    <script src="<?= ASSETS_URL ?>/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        /* ✅ Estilização aprimorada para o menu */
        .navbar-nav .nav-link {
            font-size: 18px; /* Aumenta o tamanho dos links */
            font-weight: bold; /* Deixa os links em negrito */
            padding: 10px 15px; /* Ajuste do espaçamento interno */
            transition: 0.3s; /* Efeito suave */
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Efeito ao passar o mouse */
            border-radius: 5px;
        }

        /* ✅ Estilização do botão de administração */
        .nav-item .admin-btn {
            background: black;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav-item .admin-btn:hover {
            background: #333;
            color: #fff;
        }

        /* ✅ Ajustes no submenu */
        .dropdown-menu {
            min-width: 180px;
        }
    </style>
</head>
<body>
    <header class="sticky-top shadow">
        <nav class="navbar navbar-expand-lg navbar-light bg-info">
            <div class="container-fluid">
                <!-- ✅ Logo -->
                <a class="navbar-brand" href="<?= BASE_URL ?>" aria-label="Página inicial">
                    <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>" 
                         alt="Logo da Mercearia" class="img-fluid" style="height: 50px;">
                </a>

                <!-- ✅ Botão de alternância para dispositivos móveis -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- ✅ Menu de Navegação -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>" aria-label="Página inicial">🏠 Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>produtos" aria-label="Ver produtos">🛒 Produtos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>sobre" aria-label="Sobre a Mercearia">📖 Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>contato" aria-label="Entre em contato">📞 Contato</a></li>

                        <?php if ($usuarioNivel === 'admin' || $usuarioNivel === 'funcionario'): ?>
                            <li class="nav-item">
                                <a class="nav-link admin-btn" href="<?= BASE_URL ?>admin" aria-label="Painel Administrativo">🔧 Administração</a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- ✅ Barra de Pesquisa -->
                    <form class="d-flex" action="<?= BASE_URL ?>buscar" method="GET">
                        <input class="form-control me-2" type="search" placeholder="🔍 Pesquisar..." aria-label="Digite sua pesquisa">
                        <button class="btn btn-outline-light" type="submit">Buscar</button>
                    </form>

                    <!-- ✅ Exibir Menu de Login SOMENTE SE NÃO FOR A HOME -->
                    <?php if (!$ehHome): ?>
                        <div class="dropdown ms-3">
                            <button class="btn btn-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($usuarioNome, ENT_QUOTES, 'UTF-8') ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <?php if ($usuarioLogado): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>perfil"><i class="fas fa-user"></i> Meu Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>sair"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>login"><i class="fas fa-sign-in-alt"></i> Fazer Login</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
