<?php
if (session_status() === PHP_SESSION_NONE) session_start();

error_log("[adminproduto/novo] Renderizando formulário de novo produto");

// Autenticação
if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit();
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
    header("Location: " . BASE_URL);
    exit();
}

// Definir BASE_URL
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$script}");
}

$assetsPath = BASE_URL . "/app/Assets/";

// Gera token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$categorias = [
    'hortifruti' => 'Hortifruti',
    'bebidas'    => 'Bebidas',
    'limpeza'    => 'Limpeza',
    'importados' => 'Importados',
    'padaria'    => 'Padaria Artesanal',
];
$unidades = ['Unidade', 'Kg', 'L', 'Pacote'];
$fornecedores = $fornecedores ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Novo Produto - ADM Mercearia</title>
  <link href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assetsPath ?>css/mercearia.css" rel="stylesheet">
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
    <h2 class="text-success"><i class="fas fa-plus-circle me-2"></i>Novo Produto</h2>
    <a href="<?= BASE_URL ?>/adminproduto" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
  </div>

  <form action="<?= BASE_URL ?>/adminproduto/salvar" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Abas -->
    <ul class="nav nav-tabs" id="produtoTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-dados" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-estoque" data-bs-toggle="tab" data-bs-target="#estoque" type="button" role="tab">Estoque</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-fornecedor" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">Fornecedor</button>
      </li>
    </ul>

    <div class="tab-content border border-top-0 p-4">
      <!-- Aba Dados -->
      <div class="tab-pane fade show active" id="dados" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
              <option value="">Selecione</option>
              <?php foreach ($categorias as $slug => $nome): ?>
                <option value="<?= $slug ?>"><?= $nome ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Preço de Venda (€)</label>
            <input type="number" step="0.01" name="preco" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Unidade</label>
            <select name="unidade" class="form-select" required>
              <option value="">Selecione</option>
              <?php foreach ($unidades as $u): ?>
                <option value="<?= $u ?>"><?= $u ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="ativo" selected>Ativo</option>
              <option value="inativo">Inativo</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Imagem</label>
            <input type="file" name="imagem" class="form-control">
          </div>
        </div>
      </div>

      <!-- Aba Estoque -->
      <div class="tab-pane fade" id="estoque" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Quantidade</label>
            <input type="number" name="quantidade" class="form-control" value="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Estoque Mínimo</label>
            <input type="number" name="estoque_minimo" class="form-control" value="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Validade</label>
            <input type="date" name="validade" class="form-control">
          </div>
        </div>
      </div>

      <!-- Aba Fornecedor -->
      <div class="tab-pane fade" id="fornecedor" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Fornecedor</label>
            <select name="fornecedor_id" class="form-select" required>
              <option value="">Selecione</option>
              <?php foreach ($fornecedores as $f): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Custo (€)</label>
            <input type="number" step="0.01" name="custo" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">NIPC</label>
            <input type="text" name="nipc" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <a href="<?= BASE_URL ?>/adminproduto" class="btn btn-secondary me-2">Cancelar</a>
      <button type="submit" class="btn btn-success">Salvar Produto</button>
    </div>
  </form>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
