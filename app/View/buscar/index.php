<?php
// ============================
// 🔒 Iniciar Sessão e Verificar Autenticação
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado.");
}

// ============================
// 📌 Definir BASE_URL se não estiver definida
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

// ============================
// 🔎 Capturar Variáveis
// ============================
$produtos = $produtos ?? [];
$termo = htmlspecialchars($termo ?? '', ENT_QUOTES, 'UTF-8');

// Caminho dos assets
$assetsPath = BASE_URL . "app/Assets/";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar: <?= $termo ?></title>

    <!-- ✅ Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetsPath . 'css/mercearia.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <!-- ✅ Inclui o Cabeçalho -->
    <?php include_once __DIR__ . "/../include/header.php"; ?>

    <div class="container mt-4">
        <h2 class="text-primary">Resultados para: <strong><?= $termo ?></strong></h2>

        <?php if (empty($produtos)): ?>
            <div class="alert alert-warning">Nenhum produto encontrado para "<strong><?= $termo ?></strong>".</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <p class="card-text"><?= htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="card-text"><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <a href="<?= BASE_URL ?>produto/detalhes/<?= urlencode($produto['id']) ?>" class="btn btn-primary">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ✅ Inclui o Rodapé -->
    <?php include_once __DIR__ . "/../include/footer.php"; ?>
</body>
</html>
