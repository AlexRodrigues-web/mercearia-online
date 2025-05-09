<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined("MERCEARIA2025")) {
    die("Erro: Acesso não autorizado.");
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

$produtos = $produtos ?? [];
$termo = htmlspecialchars($termo ?? '', ENT_QUOTES, 'UTF-8');
$assetsPath = BASE_URL . "app/Assets/";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar: <?= $termo ?></title>

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia-custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include_once __DIR__ . "/../include/header.php"; ?>

<div class="container py-4">
    <h2 class="text-primary mb-4">Resultados da busca por: <strong><?= $termo ?></strong></h2>

    <?php if (empty($produtos)): ?>
        <div class="alert alert-warning text-center">Nenhum produto encontrado para "<strong><?= $termo ?></strong>".</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($produtos as $produto): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm produto-card">
                        <img src="<?= $produto['imagem'] ?? $assetsPath . 'image/produtos/produto_default.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($produto['nome']) ?></h5>
                            <p class="text-success fw-bold mb-1">€ <?= number_format((float)($produto['preco'] ?? 0), 2, ',', '.') ?></p>
                            <small class="text-muted d-block mb-2"><?= htmlspecialchars($produto['descricao'] ?? 'Sem descrição') ?></small>
                            <button 
                                type="button"
                                class="btn btn-success btn-sm w-100 mt-2 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                onclick='abrirModalCarrinho(<?= json_encode(["id" => $produto["id"]]) ?>)'>
                                <i class="fas fa-cart-plus fa-lg"></i>
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL Adicionar ao Carrinho -->
<div class="modal fade" id="modalCarrinho" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Selecionar Quantidade</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="formAdicionarCarrinho" method="POST">
            <input type="hidden" name="produto_id" id="produto_id">
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" min="1" value="1" class="form-control" name="quantidade" id="quantidade" required>
            </div>
            <div class="mb-3">
                <label for="unidade" class="form-label">Unidade</label>
                <select name="unidade" id="unidade" class="form-select">
                    <option value="unidade">Unidade</option>
                    <option value="kg">Kg</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">✅ Adicionar ao Carrinho</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- TOAST de Confirmação -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastCarrinho" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
        <div class="d-flex">
            <div class="toast-body">
                Produto adicionado ao carrinho com sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../include/footer.php"; ?>

<!-- Scripts -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= $assetsPath ?>js/eventos.js"></script>

</body>
</html>
