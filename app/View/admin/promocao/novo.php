<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', "{$proto}://{$_SERVER['HTTP_HOST']}" . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');
}

if (empty($_SESSION['usuario']['logado']) || !$_SESSION['usuario']['logado']) {
    header("Location: " . BASE_URL . "login"); exit();
}

if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin','funcionario'], true)) {
    header("Location: " . BASE_URL); exit();
}

$assets = BASE_URL . "app/Assets/";
$produtos = $dados['produtos'] ?? [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Nova Promoção</title>
  <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assets ?>css/mercearia.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a2d9d6f1f0.js" crossorigin="anonymous"></script>
  <style>
    .hidden { display: none; }
    body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    label { font-weight: 500; }
    .info-box { background: #e9f7ef; border-left: 5px solid #28a745; padding: 1rem; border-radius: 6px; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/admin">
    <i class="fas fa-tools me-2"></i> ADM
  </a>
</nav>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-success"><i class="fas fa-plus-circle me-2"></i>Novo Produto</h2>
    <a href="<?= BASE_URL ?>/adminproduto" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
  </div>
  
<main class="container py-5">
  <div class="mb-4">
    <h3 class="text-success"><i class="fas fa-tags me-2"></i>Nova Promoção</h3>
    <?php if (!empty($msg)): ?>
      <div class="alert alert-info alert-dismissible fade show mt-2">
        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <form method="POST" action="<?= BASE_URL ?>adminpromocao/salvar" class="bg-white p-4 rounded shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
      <label for="produto_id">Produto</label>
      <select id="produto_id" name="produto_id" class="form-select" required>
        <option value="">Selecione um produto...</option>
        <?php foreach ($produtos as $prod): ?>
          <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="tipo">Tipo de Promoção</label>
        <select name="tipo" id="tipo" class="form-select" required>
          <option value="percentual">Desconto Percentual (%)</option>
          <option value="fixo">Desconto Fixo (€)</option>
          <option value="compre2leve3">Compre 3 Leve 2</option>
          <option value="fretegratis">Frete Grátis</option>
          <option value="ofertadodia">Oferta do Dia</option>
        </select>
      </div>

      <div class="col-md-6 mb-3" id="campoPercentual">
        <label for="desconto_percentual">Desconto (%)</label>
        <input type="number" name="desconto_percentual" id="desconto_percentual" class="form-control" min="1" max="100">
      </div>

      <div class="col-md-6 mb-3 hidden" id="campoFixo">
        <label for="desconto_fixo">Desconto Fixo (€)</label>
        <input type="number" name="desconto_fixo" id="desconto_fixo" class="form-control" min="0.01" step="0.01">
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-3">
        <label for="dt_inicio_data">Data de Início</label>
        <input type="date" name="dt_inicio" id="dt_inicio_data" class="form-control" required>
      </div>
      <div class="col-md-3 mb-3">
        <label for="dt_inicio_hora">Hora de Início</label>
        <input type="time" name="dt_inicio_hora" id="dt_inicio_hora" class="form-control" required>
      </div>
      <div class="col-md-3 mb-3">
        <label for="dt_fim_data">Data Final</label>
        <input type="date" name="dt_fim" id="dt_fim_data" class="form-control" required>
      </div>
      <div class="col-md-3 mb-3">
        <label for="dt_fim_hora">Hora Final</label>
        <input type="time" name="dt_fim_hora" id="dt_fim_hora" class="form-control" required>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="selo">Selo Visual</label>
        <select name="selo" id="selo" class="form-select">
          <option value="">Selecione um selo...</option>
          <option value="Oferta Especial">Oferta Especial</option>
          <option value="Só Hoje">Só Hoje</option>
          <option value="Imperdível">Imperdível</option>
          <option value="Últimas Unidades">Últimas Unidades</option>
          <option value="Exclusivo Online">Exclusivo Online</option>
          <option value="Desconto Relâmpago">Desconto Relâmpago</option>
          <option value="Novidade">Novidade</option>
          <option value="Lançamento">Lançamento</option>
          <option value="Leve + Pague -">Leve + Pague -</option>
          <option value="Frete Grátis">Frete Grátis</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label for="ordem">Ordem de Exibição</label>
        <input type="number" name="ordem" id="ordem" class="form-control" min="1" value="1">
      </div>
    </div>

    <!-- Bloco profissional fake para preencher o espaço removido -->
    <div class="info-box mb-4">
      <strong>📌 Regras da Promoção:</strong>
      <ul class="mb-0 mt-2 ps-3">
        <li>Promoções visíveis em todas as plataformas.</li>
        <li>Aplicável apenas durante o período definido.</li>
        <li>Descontos não cumulativos com outras campanhas.</li>
      </ul>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="<?= BASE_URL ?>adminpromocao" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
      </a>
      <div>
        <a href="<?= BASE_URL ?>adminpromocao" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Salvar</button>
      </div>
    </div>
  </form>
</main>

<script>
document.getElementById('tipo').addEventListener('change', function () {
  const val = this.value;
  document.getElementById('campoPercentual').classList.toggle('hidden', val !== 'percentual');
  document.getElementById('campoFixo').classList.toggle('hidden', val !== 'fixo');
});
</script>
<script src="<?= $assets ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
