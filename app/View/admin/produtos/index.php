<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Autenticação e autorização
if (!($_SESSION['usuario']['logado'] ?? false)) {
    header("Location: " . BASE_URL . "login");
    exit();
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
    header("Location: " . BASE_URL);
    exit();
}

// Definição de BASE_URL caso não exista
if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', "{$proto}://{$_SERVER['HTTP_HOST']}" . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}

$assets       = BASE_URL . "/app/Assets/";
$produtos     = $produtos ?? [];
$search       = trim($_GET['q'] ?? '');
$catFilter    = $_GET['categoria'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$categorias = [
    'hortifruti' => 'Hortifruti',
    'bebidas'    => 'Bebidas',
    'limpeza'    => 'Limpeza',
    'importados' => 'Importados',
    'padaria'    => 'Padaria Artesanal',
];

// Filtragem
$produtosFiltrados = array_filter($produtos, function($p) use ($search, $catFilter, $statusFilter) {
    $matchSearch = !$search || stripos($p['nome'], $search) !== false || stripos($p['sku'] ?? '', $search) !== false;
    $matchCat    = !$catFilter || strcasecmp($p['categoria'] ?? '', $catFilter) === 0;
    $matchStat   = !$statusFilter || strcasecmp($p['status'] ?? '', $statusFilter) === 0;
    return $matchSearch && $matchCat && $matchStat;
});

// Contadores
$totais = ['ativo'=>0, 'esgotado'=>0, 'inativo'=>0];
foreach ($produtos as $p) {
    $st = strtolower($p['status'] ?? 'inativo');
    if (isset($totais[$st])) {
        $totais[$st]++;
    }
}

// Flash messages
$flashSuccess = $_SESSION['msg_sucesso'] ?? '';
$flashError   = is_array($_SESSION['msg_erro'] ?? null)
                ? ($_SESSION['msg_erro'][0] ?? '')
                : ($_SESSION['msg_erro'] ?? '');
unset($_SESSION['msg_sucesso'], $_SESSION['msg_erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gestão de Produtos</title>
  <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assets ?>css/mercearia.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a2d9d6f1f0.js" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/admin">
        <i class="fas fa-tools me-2"></i> ADM
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/admin" title="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminproduto" title="Produtos">
                    <i class="fas fa-boxes"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminvenda" title="Vendas">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/adminusuario" title="Usuários">
                    <i class="fas fa-users"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="gestaoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Gestão">
                    <i class="fas fa-cogs"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="gestaoDropdown">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/funcionario"><i class="fas fa-id-badge me-2"></i>Funcionários</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/nivel"><i class="fas fa-layer-group me-2"></i>Níveis</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/estoque"><i class="fas fa-warehouse me-2"></i>Estoque</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/fornecedor"><i class="fas fa-truck me-2"></i>Fornecedores</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/promocao"><i class="fas fa-tags me-2"></i>Promoções</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/relatorios"><i class="fas fa-chart-line me-2"></i>Relatórios</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/configuracoes"><i class="fas fa-sliders-h me-2"></i>Configurações</a></li>
                </ul>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/sair" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="container-fluid mt-4">
  <!-- TÍTULO E AÇÕES -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-success"><i class="fas fa-boxes me-2"></i>Produtos</h2>
    <div>
      <a href="<?= BASE_URL ?>/adminproduto/novo" class="btn btn-success me-2">
        <i class="fas fa-plus"></i> Novo Produto
      </a>
      <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="fas fa-file-export"></i> Exportar
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="<?= BASE_URL ?>/adminproduto/exportar?formato=csv"><i class="fas fa-file-csv me-1"></i>CSV</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>/adminproduto/exportar?formato=pdf"><i class="fas fa-file-pdf me-1"></i>PDF</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- FLASH MESSAGES -->
  <?php if ($flashSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flashSuccess) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($flashError): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flashError) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- INDICADORES -->
  <div class="row mb-4 gx-2">
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <i class="fas fa-list fs-1 text-primary"></i>
          <h5>Total</h5>
          <h3><?= count($produtos) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <i class="fas fa-check-circle fs-1 text-success"></i>
          <h5>Ativos</h5>
          <h3><?= $totais['ativo'] ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <i class="fas fa-exclamation-triangle fs-1 text-warning"></i>
          <h5>Esgotados</h5>
          <h3><?= $totais['esgotado'] ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <i class="fas fa-times-circle fs-1 text-secondary"></i>
          <h5>Inativos</h5>
          <h3><?= $totais['inativo'] ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- FILTROS -->
  <form method="get" class="row g-2 align-items-end mb-4">
    <div class="col-md-4">
      <label for="q" class="form-label">Buscar</label>
      <input type="text" id="q" name="q" class="form-control" placeholder="Nome ou SKU" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
      <label for="categoria" class="form-label">Categoria</label>
      <select id="categoria" name="categoria" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($categorias as $slug => $nome): ?>
          <option value="<?= $slug ?>" <?= $catFilter === $slug ? 'selected' : '' ?>><?= $nome ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="status" class="form-label">Status</label>
      <select id="status" name="status" class="form-select">
        <option value="">Todos</option>
        <option value="ativo"    <?= $statusFilter === 'ativo'   ? 'selected' : '' ?>>Ativo</option>
        <option value="esgotado" <?= $statusFilter === 'esgotado'? 'selected' : '' ?>>Esgotado</option>
        <option value="inativo"  <?= $statusFilter === 'inativo' ? 'selected' : '' ?>>Inativo</option>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter me-1"></i> Filtrar
      </button>
    </div>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary">← Voltar ao Painel</a>
  </form>

  <!-- TABELA -->
  <div class="table-responsive shadow-sm">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th><th>SKU</th><th>Nome</th><th>Categoria</th><th>Preço</th>
          <th>Qtd.</th><th>Min.</th><th>Status</th><th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($produtosFiltrados)): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">Nenhum produto encontrado</td></tr>
        <?php else: ?>
          <?php foreach ($produtosFiltrados as $p): 
            $st    = strtolower($p['status'] ?? 'inativo');
            $classes = ['ativo'=>'success','esgotado'=>'warning','inativo'=>'secondary'];
            $badge   = $classes[$st] ?? 'dark';
            $label   = ucfirst($st);
          ?>
            <tr>
              <td><?= (int)$p['id'] ?></td>
              <td><?= htmlspecialchars($p['sku'] ?? '-') ?></td>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
              <td>€<?= number_format($p['preco'],2,',','.') ?></td>
              <td><?= (int)($p['estoque'] ?? 0) ?></td>
              <td><?= (int)($p['estoque_minimo'] ?? 0) ?></td>
              <td><span class="badge bg-<?= $badge ?>"><?= $label ?></span></td>
              <td>
                <div class="btn-group" role="group">
                  <a href="<?= BASE_URL ?>/adminproduto/editar?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Mais</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/adminproduto/historico?id=<?= $p['id'] ?>"><i class="fas fa-history me-1"></i>Histórico</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/adminproduto/qr?id=<?= $p['id'] ?>"><i class="fas fa-qrcode me-1"></i>QR Code</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/adminproduto/excluir?id=<?= $p['id'] ?>" onclick="return confirm('Confirma exclusão?')"><i class="fas fa-trash-alt me-1"></i>Excluir</a></li>
                  </ul>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<script src="<?= $assets ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
  });
</script>
</body>
</html>
