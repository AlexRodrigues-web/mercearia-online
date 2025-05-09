<?php 
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

$assetsPath = BASE_URL . "app/Assets/";
$basePath = __DIR__ . "/../include/";
$imagemPadrao = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia - Bem-vindo</title>

    <!-- Google Font funcional e otimizada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            position: relative;
            background: url('<?= $assetsPath ?>image/banners/banner.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 60px 20px;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 1;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(50, 50, 50, 0.6), rgba(234, 10, 10, 0.6));
            z-index: -2;
        }

        .categoria-icon {
            font-size: 2rem;
            color:rgb(34, 43, 224);
        }
        .card-produto img {
            height: 180px;
            object-fit: cover;
        }
        /* Estilos para o carrossel */
        .carousel-section img {
            max-height: 300px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include $basePath . "header.php"; ?>

    <!-- Nova seção: Carrossel de Imagens -->
    <section class="carousel-section">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?= $assetsPath ?>image/banners/banner1.jpg" class="d-block w-100" alt="Banner 1">
                </div>
                <div class="carousel-item">
                    <img src="<?= $assetsPath ?>image/banners/banner2.jpg" class="d-block w-100" alt="Banner 2">
                </div>
                <div class="carousel-item">
                    <img src="<?= $assetsPath ?>image/banners/banner3.jpg" class="d-block w-100" alt="Banner 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>
    </section>
    <!-- Fim nova seção: Carrossel -->

    <div class="content">

        <div class="container mt-3">
            <?php include $basePath . "mensagens.php"; ?>
        </div>

        <!-- Banner Hero -->
        <section class="hero-section text-center py-5">
            <div class="container">
                <h1 class="display-5 fw-bold">🍎 Produtos fresquinhos todos os dias para sua família!</h1>
                <p class="lead">Atendendo com carinho clientes em Portugal e no Brasil.</p>
                <a href="<?= BASE_URL ?>produtos" class="btn btn-light btn-lg mt-3">Ver Produtos</a>
            </div>
        </section>

        <!-- Categorias -->
        <section class="container my-5">
            <h2 class="text-center mb-4"><i class="fas fa-list"></i> Categorias em Destaque</h2>
            <div class="row g-4 text-center">
                <?php
                $categorias = [
                    ['icon' => 'apple-alt', 'title' => 'Hortifruti'],
                    ['icon' => 'wine-bottle', 'title' => 'Bebidas'],
                    ['icon' => 'soap', 'title' => 'Limpeza'],
                    ['icon' => 'globe', 'title' => 'Importados 🇧🇷🇵🇹'],
                ];
                foreach ($categorias as $cat): ?>
                    <div class="col-6 col-md-3">
                        <div class="bg-light border rounded p-4 shadow-sm h-100">
                            <i class="fas fa-<?= $cat['icon'] ?> categoria-icon mb-2"></i>
                            <h6><?= $cat['title'] ?></h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Promoções da Semana (atualizado) -->
        <section class="container my-5">
            <h2 class="text-center mb-4"><i class="fas fa-tags"></i> Promoções da Semana</h2>
            <div class="row g-4">
                <?php 
                $categoriasPromo = ['Hortifruti', 'Bebidas', 'Limpeza', 'Importados'];
                $promocoes = [];
                if (!empty($dados['produtos'])) {
                    foreach ($dados['produtos'] as $produto) {
                        $produtoCategoria = trim(strip_tags($produto['categoria'] ?? ''));
                        if (in_array($produtoCategoria, $categoriasPromo) && !isset($promocoes[$produtoCategoria])) {
                            $promocoes[$produtoCategoria] = $produto;
                        }
                    }
                }
                foreach ($categoriasPromo as $categoria): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 card-produto">
                            <?php if (isset($promocoes[$categoria])): ?>
                                <img src="<?= BASE_URL ?>app/Assets/image/produtos/<?= $promocoes[$categoria]['imagem'] ?? 'produto_default.jpg' ?>" class="card-img-top" alt="<?= $promocoes[$categoria]['nome'] ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= $promocoes[$categoria]['nome'] ?></h5>
                                    <a href="<?= BASE_URL ?>produtos" class="btn btn-sm btn-success">Ver Produto</a>
                                </div>
                            <?php else: ?>
                                <div class="card-body text-center">
                                    <h5 class="card-title">Sem Promoção</h5>
                                    <p class="text-muted"><?= $categoria ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Destaque: Padaria Artesanal -->
        <section class="bg-warning-subtle py-5">
            <div class="container text-center">
                <h2 class="mb-4"><i class="fas fa-bread-slice"></i> Novidade: Padaria Artesanal</h2>
                <p class="mb-3 fs-5">Experimente nossos pães frescos, produzidos com ingredientes selecionados e fermentação natural.</p>
                <img src="<?= BASE_URL ?>app/Assets/image/produtos/Pão artesanal.jpg" alt="Pão artesanal" class="img-fluid rounded shadow-sm mb-3" style="max-height: 300px;">
                <div>
                    <a href="<?= BASE_URL ?>produtos?categoria=padaria" class="btn btn-outline-success">Ver Produtos da Padaria</a>
                </div>
            </div>
        </section>

        <!-- Como funciona -->
        <section class="bg-light py-5">
            <div class="container text-center">
                <h2 class="mb-4"><i class="fas fa-sync-alt"></i> Como Funciona</h2>
                <div class="row g-4">
                    <?php
                    $etapas = [
                        ['icon' => 'search', 'desc' => 'Navegue'],
                        ['icon' => 'shopping-cart', 'desc' => 'Peça'],
                        ['icon' => 'box', 'desc' => 'Preparamos'],
                        ['icon' => 'smile', 'desc' => 'Receba'],
                    ];
                    foreach ($etapas as $step): ?>
                        <div class="col-6 col-md-3">
                            <div class="bg-white border p-3 rounded shadow-sm h-100">
                                <i class="fas fa-<?= $step['icon'] ?> fa-2x text-success mb-2"></i>
                                <p class="fw-semibold"><?= $step['desc'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Avaliações -->
        <section class="container my-5">
            <h2 class="text-center mb-4"><i class="fas fa-star"></i> O que dizem nossos clientes</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <blockquote class="blockquote bg-light border p-4 rounded shadow-sm">
                        <p>"Produtos sempre frescos e o atendimento é excelente!"</p>
                        <footer class="blockquote-footer">Ana, Vila Nova de Gaia</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote bg-light border p-4 rounded shadow-sm">
                        <p>"Amo a variedade e os preços justos. Super recomendo."</p>
                        <footer class="blockquote-footer">Carlos, Lisboa</footer>
                    </blockquote>
                </div>
            </div>
        </section>

        <!-- Vantagens -->
        <section class="bg-success text-white py-5">
            <div class="container text-center">
                <h2 class="mb-4"><i class="fas fa-handshake"></i> Por que comprar conosco?</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <p><i class="fas fa-truck fa-fw me-2"></i> Entrega rápida e segura</p>
                    </div>
                    <div class="col-md-4">
                        <p><i class="fas fa-heart fa-fw me-2"></i> Atendimento humanizado</p>
                    </div>
                    <div class="col-md-4">
                        <p><i class="fas fa-globe-europe fa-fw me-2"></i> Tradição Brasil–Portugal</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Chamada para ação -->
        <section class="container py-5 text-center">
            <h2 class="mb-3"><i class="fas fa-shopping-basket"></i> Faça Seu Pedido Agora</h2>
            <p class="mb-3">Explore todas as categorias, adicione seus produtos favoritos ao carrinho e receba com comodidade.</p>
            <a href="<?= BASE_URL ?>produtos" class="btn btn-lg btn-success px-5">Ver Produtos</a>
        </section>

    </div>

    <?php include $basePath . "footer.php"; ?>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
