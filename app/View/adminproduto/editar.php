<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit();
}

if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
    header("Location: " . BASE_URL);
    exit();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}

$assetsPath = BASE_URL . "/app/Assets/";
$produto = $produto ?? [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

error_log("[adminproduto/editar] Editando produto ID: " . ($produto['id'] ?? 'N/I'));

$categorias = [
    'hortifruti' => 'Hortifruti',
    'bebidas'    => 'Bebidas',
    'limpeza'    => 'Limpeza',
    'importados' => 'Importados',
    'padaria'    => 'Padaria Artesanal'
];
$unidades = ['Unidade', 'Kg', 'L', 'Pacote'];
$fornecedores = $fornecedores ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Produto - ADM Mercearia</title>
  <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
  <script src="https://kit.fontawesome.com/94e238924c.js" crossorigin="anonymous"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/admin">
    <i class="fas fa-tools me-2"></i> ADM
  </a>
</nav>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-warning"><i class="fas fa-edit me-2"></i>Editar Produto</h2>
    <a href="<?= BASE_URL ?>/adminproduto" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
  </div>

  <form action="<?= BASE_URL ?>/adminproduto/atualizar" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id'] ?? '') ?>">

    <ul class="nav nav-tabs" id="produtoTab" role="tablist">
      <li class="nav-item"><button type="button" class="nav-link active" data-target="#dados">Dados</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-target="#estoque">Estoque</button></li>
      <li class="nav-item"><button type="button" class="nav-link" data-target="#fornecedor">Fornecedor</button></li>
    </ul>

    <div class="tab-content border border-top-0 p-4">
      <!-- Aba Dados -->
      <div class="tab-pane fade show active" id="dados">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
          <div class="col-md-6">
            <label for="categoria" class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
              <?php foreach ($categorias as $slug => $nome): ?>
                <option value="<?= $slug ?>" <?= (isset($produto['categoria']) && $produto['categoria'] === $slug ? 'selected' : '') ?>><?= $nome ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="preco" class="form-label">Preço de Venda (€)</label>
            <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($produto['preco'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
          <div class="col-md-6">
            <label for="unidade" class="form-label">Unidade</label>
            <select name="unidade" class="form-select">
              <?php foreach ($unidades as $u): ?>
                <option value="<?= $u ?>" <?= (isset($produto['unidade']) && $produto['unidade'] === $u ? 'selected' : '') ?>><?= $u ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="sku" class="form-label">Código SKU</label>
            <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($produto['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-12">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control"><?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
          <div class="col-md-6">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" name="imagem" class="form-control">
            <?php if (!empty($produto['imagem'])): ?>
              <img src="<?= BASE_URL ?>/app/Assets/image/produtos/<?= $produto['imagem'] ?>" class="mt-2" style="max-width:120px" alt="Imagem atual">
            <?php endif; ?>
          </div>
          <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="ativo" <?= ($produto['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
              <option value="inativo" <?= ($produto['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Aba Estoque -->
      <div class="tab-pane fade" id="estoque">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" name="quantidade" class="form-control" value="<?= htmlspecialchars($produto['quantidade'] ?? 0, ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-md-4">
            <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
            <input type="number" name="estoque_minimo" class="form-control" value="<?= htmlspecialchars($produto['estoque_minimo'] ?? 0, ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-md-4">
            <label for="validade" class="form-label">Validade</label>
            <input type="date" name="validade" class="form-control" value="<?= htmlspecialchars($produto['validade'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>

      <!-- Aba Fornecedor -->
      <div class="tab-pane fade" id="fornecedor">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="fornecedor_id" class="form-label">Fornecedor</label>
            <select name="fornecedor_id" class="form-select">
              <option value="">Selecione</option>
              <?php foreach ($fornecedores as $f): ?>
                <option value="<?= $f['id'] ?>" <?= (isset($produto['fornecedor_id']) && $produto['fornecedor_id'] == $f['id'] ? 'selected' : '') ?>>
                  <?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="custo" class="form-label">Custo</label>
            <input type="number" step="0.01" name="custo" class="form-control" value="<?= htmlspecialchars($produto['custo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-md-3">
            <label for="nipc" class="form-label">NIPC</label>
            <input type="text" name="nipc" class="form-control" value="<?= htmlspecialchars($produto['nipc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <a href="<?= BASE_URL ?>/adminproduto" class="btn btn-secondary me-2">Cancelar</a>
      <button type="submit" class="btn btn-warning">Atualizar</button>
    </div>
  </form>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Script para trocar abas sem submeter o formulário -->
<script>
document.querySelectorAll('.nav-link').forEach(function(btn) {
    btn.addEventListener('click', function(event) {
        event.preventDefault();
        const target = btn.getAttribute('data-target');
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        btn.classList.add('active');
        document.querySelector(target).classList.add('show', 'active');
    });
});
</script>

</body>
</html>
