<?php
$promo = $promo ?? [];

if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', "{$proto}://{$_SERVER['HTTP_HOST']}" . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}
$assets = BASE_URL . "/app/Assets/";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Promoção</title>
  <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assets ?>css/mercearia.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a2d9d6f1f0.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">

<main class="container py-5">
  <div class="row mb-4">
    <div class="col">
      <h2 class="text-warning"><i class="fas fa-edit me-2"></i>Editar Promoção</h2>
    </div>
  </div>

  <form method="POST" action="<?= BASE_URL ?>/adminpromocao/atualizar" class="bg-white p-4 rounded shadow-sm">
    <input type="hidden" name="id" value="<?= (int)($promo['id'] ?? 0) ?>">

    <!-- Produto (somente leitura) -->
    <div class="mb-3">
      <label class="form-label">Produto</label>
      <input type="text" class="form-control" 
             value="ID <?= $promo['produto_id'] ?? '?' ?> - <?= $promo['nome_produto'] ?? 'Produto não encontrado' ?>" readonly>
      <input type="hidden" name="produto_id" value="<?= (int)($promo['produto_id'] ?? 0) ?>">
    </div>

    <!-- Desconto -->
    <div class="mb-3">
      <label class="form-label">Desconto (%)</label>
      <input type="number" name="desconto" class="form-control"
             value="<?= htmlspecialchars($promo['desconto'] ?? 0) ?>" step="0.01" min="0" required>
    </div>

    <!-- Datas -->
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Data Início</label>
        <input type="datetime-local" name="inicio" class="form-control"
               value="<?= isset($promo['inicio']) ? date('Y-m-d\TH:i', strtotime($promo['inicio'])) : '' ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Data Fim</label>
        <input type="datetime-local" name="fim" class="form-control"
               value="<?= isset($promo['fim']) ? date('Y-m-d\TH:i', strtotime($promo['fim'])) : '' ?>" required>
      </div>
    </div>

    <!-- Ativo -->
    <div class="mb-3">
      <label class="form-label">Promoção Ativa?</label>
      <select name="ativo" class="form-select">
        <option value="1" <?= isset($promo['ativo']) && $promo['ativo'] ? 'selected' : '' ?>>Sim</option>
        <option value="0" <?= isset($promo['ativo']) && !$promo['ativo'] ? 'selected' : '' ?>>Não</option>
      </select>
    </div>

    <!-- Botões -->
    <div class="d-flex justify-content-between">
      <a href="<?= BASE_URL ?>/adminpromocao" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-1"></i> Voltar para Gestão de Promoções
      </a>
      <div>
        <a href="<?= BASE_URL ?>/adminpromocao" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-warning">Atualizar Promoção</button>
      </div>
    </div>
  </form>
</main>

<script src="<?= $assets ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
