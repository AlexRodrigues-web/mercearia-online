<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION['usuario']['logado'] ?? false)) {
    header("Location: " . BASE_URL . "login");
    exit();
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'], true)) {
    header("Location: " . BASE_URL);
    exit();
}
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
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gestão de Promoções</title>
  <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assets ?>css/mercearia.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a2d9d6f1f0.js" crossorigin="anonymous"></script>
  <style>
    .kpi-card {
      border-radius: .5rem;
      padding: 1rem;
      color: #fff;
    }
    .kpi-card .title { font-size: .9rem; text-transform: uppercase; }
    .kpi-card .value { font-size: 1.8rem; font-weight: bold; }
  </style>
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

            <!-- Acesso Rápido -->
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

            <!-- Dropdown de Gestão -->
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

            <!-- Sair -->
            <li class="nav-item ms-2">
                <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/sair" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>
<main class="container py-4">

<?php if (!empty($_SESSION['msg'])): ?>
  <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?? 'info' ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
  </div>
  <?php unset($_SESSION['msg'], $_SESSION['tipo_mensagem']); ?>
<?php endif; ?>


  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">← Voltar ao Painel</a>
    <h2 class="text-primary"><i class="fas fa-tags me-2"></i>Gestão de Promoções</h2>
    <a href="<?= BASE_URL ?>/adminpromocao/novo" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i>Nova Promoção
    </a>
  </div>

  <!-- KPIs -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="kpi-card bg-success h-100">
        <div class="title"><i class="fas fa-bullhorn me-1"></i>Ativas</div>
        <div class="value">5</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="kpi-card bg-warning h-100">
        <div class="title"><i class="fas fa-clock me-1"></i>Agendadas</div>
        <div class="value">2</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="kpi-card bg-secondary h-100">
        <div class="title"><i class="fas fa-history me-1"></i>Expiradas</div>
        <div class="value">3</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="kpi-card bg-danger h-100">
        <div class="title"><i class="fas fa-percentage me-1"></i>Maior Desconto</div>
        <div class="value">50%</div>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <form class="row g-2 align-items-end mb-4">
    <div class="col-md-4">
      <label for="filtroBusca" class="form-label">Buscar</label>
      <input id="filtroBusca" type="text" class="form-control" placeholder="título ou produto">
    </div>
    <div class="col-md-2">
      <label for="filtroStatus" class="form-label">Status</label>
      <select id="filtroStatus" class="form-select">
        <option value="">Todos</option>
        <option value="ativa">Ativa</option>
        <option value="agendada">Agendada</option>
        <option value="expirada">Expirada</option>
      </select>
    </div>
    <div class="col-md-2">
      <label for="filtroTipo" class="form-label">Tipo</label>
      <select id="filtroTipo" class="form-select">
        <option value="">Todos</option>
        <option value="percentual">Percentual</option>
        <option value="fixo">Valor Fixo</option>
        <option value="compre2leve3">Compre 3 Pague 2</option>
        <option value="fretegratis">Frete Grátis</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Período</label>
      <div class="d-flex">
        <input type="date" class="form-control me-1">
        <input type="date" class="form-control">
      </div>
    </div>
    <div class="col-md-1 d-grid">
      <button type="submit" class="btn btn-outline-primary">
        <i class="fas fa-filter"></i>
      </button>
    </div>
  </form>

  <!-- Abas -->
  <ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#ativas">Ativas</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#agendadas">Agendadas</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#expiradas">Expiradas</a></li>
  </ul>

  <div class="tab-content">

    <!-- Ativas -->
    <div class="tab-pane fade show active" id="ativas">
      <div class="table-responsive">
        <?php if (!is_array($promocoes) || empty($promocoes)): ?>
          <div class="alert alert-info">Nenhuma promoção encontrada.</div>
        <?php else: ?>
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th><th>Título</th><th>Desconto</th><th>Período</th>
                <th>Estoque</th><th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($promocoes as $promo): ?>
                <tr>
                  <td><?= (int)$promo['id'] ?></td>
                  <td><?= htmlspecialchars($promo['nome_produto']) ?></td>
                  <td><?= $promo['desconto'] ? "{$promo['desconto']}%" : '—' ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($promo['inicio'])) ?> a <?= date('d/m/Y H:i', strtotime($promo['fim'])) ?></td>
                  <td>—</td>
                  <td class="text-center">
                    <a href="<?= BASE_URL ?>/adminpromocao/editar?id=<?= $promo['id'] ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>
                    <a href="<?= BASE_URL ?>/adminpromocao/excluir?id=<?= $promo['id'] ?>" class="btn btn-sm btn-outline-danger me-1" onclick="return confirm('Confirma exclusão?');"><i class="fas fa-trash-alt"></i></a>
                    <a class="btn btn-sm btn-outline-dark"><i class="fas fa-eye"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Agendadas / Expiradas -->
    <div class="tab-pane fade" id="agendadas">
      <div class="alert alert-warning">(em desenvolvimento)</div>
    </div>
    <div class="tab-pane fade" id="expiradas">
      <div class="alert alert-secondary">(em desenvolvimento)</div>
    </div>

  </div>
</main>

<script src="<?= $assets ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
