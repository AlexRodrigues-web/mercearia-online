<?php
// ✅ Definir BASE_URL corretamente
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

// ✅ Caminhos dos assets
$assetsPath = BASE_URL . "app/Assets/";
$basePath = __DIR__ . "/../include/";

// ✅ Caminho das imagens de produtos
$imagemPadrao = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercearia - Bem-vindo</title>

    <!-- ✅ Estilos e FontAwesome -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="wrapper">
        <!-- ✅ Cabeçalho -->
        <?php include $basePath . "header.php"; ?>

        <!-- ✅ Container do conteúdo principal -->
        <div class="content">
            <!-- ✅ Mensagens (Caso existam notificações) -->
            <div class="container mt-3">
                <?php include $basePath . "mensagens.php"; ?>
            </div>

            <!-- ✅ Área de Produtos -->
            <div class="container text-center mt-5">
                <h1>Bem-vindo à Mercearia</h1>
                <p>Explore nossos produtos e faça suas compras com facilidade.</p>
                <a href="<?= BASE_URL ?>produtos" class="btn btn-success btn-lg">Ver Produtos</a>
            </div>

            <!-- ✅ Carrossel de Produtos com Imagens -->
            <div class="container mt-5">
                <div id="carouselProdutos" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php if (!empty($dados['produtos'])): ?>
                            <?php foreach ($dados['produtos'] as $index => $produto) : ?>
                                <div class="carousel-item <?= ($index === 0) ? 'active' : ''; ?>">
                                    <img src="<?= BASE_URL . 'app/Assets/image/produtos/' . htmlspecialchars($produto['imagem'] ?? 'produto_default.jpg'); ?>" 
                                         class="d-block w-100" 
                                         alt="<?= htmlspecialchars($produto['nome']); ?>">
                                    <div class="carousel-caption">
                                        <h5><?= htmlspecialchars($produto['nome']); ?></h5>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-item active">
                                <img src="<?= $imagemPadrao ?>" class="d-block w-100" alt="Nenhum produto encontrado">
                                <div class="carousel-caption">
                                    <h5>Nenhum produto disponível</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselProdutos" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselProdutos" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ Rodapé -->
        <?php include $basePath . "footer.php"; ?>
    </div>

    <!-- ✅ Scripts -->
    <script src="<?= htmlspecialchars($assetsPath . 'bootstrap/js/bootstrap.bundle.min.js', ENT_QUOTES, 'UTF-8') ?>"></script>

</body>
</html>
