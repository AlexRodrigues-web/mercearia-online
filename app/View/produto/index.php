<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

$assetsPath = BASE_URL . "app/Assets/";
$imagemPadrao = BASE_URL . "app/Assets/image/produtos/produto_default.jpg";

$msgSuccess = $_SESSION['msg_success'] ?? '';
$msgError = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

$produtos = $produtos ?? [];
error_log("[View produto/index.php] 🧾 Total de produtos recebidos: " . count($produtos));

// Gera categorias dinamicamente com base nos produtos ativos
$categorias = [];
foreach ($produtos as $p) {
    $cat = ucfirst(trim($p['categoria'] ?? 'Outros'));
    if (!in_array($cat, $categorias)) {
        $categorias[] = $cat;
    }
}
sort($categorias);

// Agrupa produtos por categoria
$produtosPorCategoria = [];
foreach ($categorias as $cat) {
    $produtosPorCategoria[$cat] = array_filter($produtos, fn($p) =>
        isset($p['categoria']) && strtolower(trim($p['categoria'])) === strtolower(trim($cat))
    );
    error_log("[View produto/index.php] 📦 Categoria: {$cat} - Total: " . count($produtosPorCategoria[$cat]));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Mercearia</title>

    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia-custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include_once __DIR__ . "/../include/header.php"; ?>

<div class="container py-5">
    <h1 class="text-center text-success mb-4"><i class="fas fa-store"></i> Nossos Produtos</h1>
    <p class="text-center text-muted mb-5">Explore os melhores produtos organizados por setor</p>

    <?php if (!empty($msgSuccess) || !empty($msgError)): ?>
        <div class="alert <?= !empty($msgSuccess) ? 'alert-success' : 'alert-danger' ?> text-center">
            <?= htmlspecialchars($msgSuccess ?: $msgError) ?>
        </div>
    <?php endif; ?>

    <?php foreach ($categorias as $categoria): ?>
        <?php if (!empty($produtosPorCategoria[$categoria])): ?>
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary mb-0"><i class="fas fa-tag"></i> <?= $categoria ?></h3>
                    <a href="#<?= strtolower(str_replace(' ', '', $categoria)) ?>" class="btn btn-outline-primary btn-sm">Ir para seção</a>
                </div>

                <div class="row g-4" id="<?= strtolower(str_replace(' ', '', $categoria)) ?>">
                    <?php foreach ($produtosPorCategoria[$categoria] as $produto): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm produto-card">
                                <img src="<?= $produto['imagem'] ?>" class="card-img-top" alt="<?= $produto['nome'] ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title mb-1"><?= $produto['nome'] ?></h5>
                                    <p class="text-success fw-bold mb-1">€ <?= number_format((float)$produto['preco'], 2, ',', '.') ?></p>
                                    <small class="text-muted d-block mb-2">Fornecedor: <?= $produto['fornecedor'] ?></small>

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
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($produtos)): ?>
        <p class="text-center text-muted">Nenhum produto disponível no momento.</p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['usuario_logado']) && in_array($_SESSION['usuario_nivel'], ['admin', 'funcionario'])): ?>
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>admin/produtos" class="btn btn-primary">
                <i class="fas fa-boxes"></i> Gerenciar Produtos
            </a>
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
    <div id="toastCarrinho" class="toast align-items-center text-bg-success border-0"
        role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-delay="3000">
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
<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= $assetsPath ?>js/eventos.js"></script>

</body>
</html>
