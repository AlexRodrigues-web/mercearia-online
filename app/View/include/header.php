<?php 
if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado. Contate o administrador.");
}

if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim(BASE_URL, '/') . '/app/Assets');
}

error_log("🕵️‍♂️ HEADER carregado - URI atual: " . $_SERVER['REQUEST_URI']);

$logoPath = ASSETS_URL . '/image/logo/logo.jpg';
$defaultLogoPath = ASSETS_URL . '/image/logo/default-logo.jpg';
$logoFullPath = $_SERVER['DOCUMENT_ROOT'] . '/app/Assets/image/logo/logo.jpg';

if (!file_exists($logoFullPath)) {
    $logoPath = $defaultLogoPath;
}

$usuarioLogado = isset($_SESSION['usuario']['id']);
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Visitante';
$usuarioNivel = $_SESSION['usuario_nivel'] ?? 'comum';

$paginaAtual = strtolower($_SERVER['REQUEST_URI']);
$ehHome = (strpos($paginaAtual, "home") !== false || $paginaAtual === BASE_URL);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia - Bem-vindo</title>

    <script>
    window.BASE_URL = "<?php echo BASE_URL; ?>";
    </script>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mercearia.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/mercearia-custom.css">

    <!-- Scripts -->
    <script src="<?= ASSETS_URL ?>/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg,rgb(0, 0, 0),rgb(69, 69, 69),rgb(115, 114, 114));
            backdrop-filter: blur(3px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 999;
            transition: background 0.3s ease-in-out;
        }

        .navbar-brand img {
            height: 50px;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            font-size: 16px;
            padding: 8px 14px;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.15);
            border-radius: 5px;
        }

        .form-control-sm {
            min-width: 150px;
        }

        .btn-outline-light {
            border-color: #fff;
            color: #fff;
        }

        .btn-outline-light:hover {
            background: #fff;
            color: #009739;
        }

        .cart-icon .badge {
            font-size: 0.75rem;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                margin-top: 10px;
            }
            .form-control-sm {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<header class="sticky-top shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4 py-2">

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" class="me-2">
                <span class="fw-bold text-white" style="font-size: 1.3rem;">Mercearia</span>
            </a>

            <!-- Botão Mobile -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuTopo">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="menuTopo">
                <ul class="navbar-nav mx-auto gap-2">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>">Início</a></li>

                    <!-- ✅ Corrigido: link funcional + dropdown -->
                    <li class="nav-item dropdown">
                    <li class="nav-item dropdown">
    <a class="nav-link" href="<?= BASE_URL ?>produtos">Produtos</a>
    <a class="nav-link dropdown-toggle d-inline d-lg-none" href="#" data-bs-toggle="dropdown"></a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="<?= BASE_URL ?>produtos#hortifruti">Hortifruti</a></li>
        <li><a class="dropdown-item" href="<?= BASE_URL ?>produtos#bebidas">Bebidas</a></li>
        <li><a class="dropdown-item" href="<?= BASE_URL ?>produtos#limpeza">Limpeza</a></li>
        <li><a class="dropdown-item" href="<?= BASE_URL ?>produtos#importados">Importados</a></li>
        <li><a class="dropdown-item fw-bold text-success" href="<?= BASE_URL ?>produtos#padaria">Padaria Artesanal</a></li>
    </ul>
</li>


                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>promocoes">Promoções</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>sobre">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>contato">Contato</a></li>
                </ul>

                <!-- Ações: Busca, Carrinho, Ajuda, Login -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">

                    <!-- Busca Moderna -->
                   <!-- Busca Moderna Corrigida -->
                    <form method="GET" action="<?= BASE_URL ?>buscar" class="d-flex input-group input-group-sm shadow-sm" style="max-width: 250px;">
                        <input type="text" name="termo" class="form-control border-0 rounded-start-pill px-3"
                             placeholder="Buscar produtos..." aria-label="Buscar" required>
                        <button class="btn btn-light border-0 rounded-end-pill px-3" type="submit">
                             <i class="fas fa-search text-success"></i>
                        </button>
                    </form>


                    <!-- Carrinho -->
                    <?php
                    $carrinhoQtd = 0;
                    if (!empty($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
                        foreach ($_SESSION['carrinho'] as $item) {
                            $carrinhoQtd += (int) $item['quantidade'];
                        }
                    }
                    ?>
                    <a href="<?= BASE_URL ?>carrinho" class="text-decoration-none position-relative me-2">
                        <i class="fas fa-shopping-cart fa-lg text-white"></i>
                        <?php if ($carrinhoQtd > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $carrinhoQtd ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Ajuda -->
                    <a href="<?= BASE_URL ?>ajuda" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-question-circle me-1"></i> Ajuda
                    </a>

                    <!-- Login / Perfil -->
                    <?php if ($usuarioLogado): ?>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($usuarioNome) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>perfil">👤 Meu Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>sair">🚪 Sair</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>login" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-in-alt me-1"></i> Entrar
                        </a>
                    <?php endif; ?>

                    <!-- Painel Admin -->
                    <?php if (in_array($usuarioNivel, ['admin', 'funcionario'])): ?>
                        <a href="<?= BASE_URL ?>admin" class="btn btn-warning btn-sm fw-bold ms-1">
                            ⚙ Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
