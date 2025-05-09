<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Autenticação e autorização
if (!($_SESSION['usuario']['logado'] ?? false)) {
    header("Location: " . BASE_URL . "login");
    exit;
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'], true)) {
    header("Location: " . BASE_URL);
    exit;
}

if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$proto}://{$host}{$scriptDir}/");
}

$assets = BASE_URL . "app/Assets/";

// === DADOS FAKE PARA ILUSTRAÇÃO ===
// KPIs
$kpis = [
    'vendas_dia'      => 1245.67,
    'vendas_mes'      => 23456.78,
    'faturamento_ano' => 345678.90,
    'total_pedidos'   => 312,
    'ticket_medio'    => 75.32,
];
// Produtos mais vendidos
$maisVendidos = [
    ['nome'=>'Pão Artesanal','quantidade'=>320],
    ['nome'=>'Suco Natural','quantidade'=>210],
    ['nome'=>'Queijo Minas','quantidade'=>180],
];
// Pedidos (detalhado)
$pedidos = [
    [
      'id'=>1023,
      'data_hora'=>'2025-05-02 14:23',
      'cliente_nome'=>'João Oliveira',
      'cliente_email'=>'joao@example.com',
      'itens'=>[
        ['nome'=>'Pão Artesanal','qtd'=>2,'unit'=>1.50],
        ['nome'=>'Suco Natural','qtd'=>1,'unit'=>2.00],
      ],
      'total'=>5.00,
      'desconto'=>0.50,
      'pagamento'=>'MB Way',
      'status_pag'=>'Pago',
      'status_ent'=>'Entregue',
    ],
    [
      'id'=>1024,
      'data_hora'=>'2025-05-02 15:10',
      'cliente_nome'=>'Maria Silva',
      'cliente_email'=>'maria@example.com',
      'itens'=>[
        ['nome'=>'Queijo Minas','qtd'=>1,'unit'=>3.00],
      ],
      'total'=>3.00,
      'desconto'=>0.00,
      'pagamento'=>'Cartão',
      'status_pag'=>'Pendente',
      'status_ent'=>'Em preparação',
    ],
];

// Dados para o gráfico (vendas por mês)
$graficoLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
$graficoData   = [1500, 2300, 1800, 2100, 2400, 2600, 2200, 2800, 3000, 3200, 3400, 3600];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vendas — Painel ADM</title>
  <link rel="stylesheet" href="<?= $assets ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assets ?>css/mercearia.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .card-dashboard { border-left:5px solid #198754; background:#f8f9fa; border-radius:.5rem; }
    .card-dashboard .card-body{padding:1.25rem;}
    .card-title{font-weight:bold;font-size:1rem;margin-bottom:.5rem;}
    .badge-status{font-size:.85em;}
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

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-success"><i class="fas fa-chart-line me-2"></i>Dashboard de Vendas</h2>
    <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">&larr; Voltar</a>
  </div>

  <!-- KPIs -->
  <div class="row g-3 mb-5">
    <div class="col-md-4">
      <div class="card card-dashboard text-center">
        <div class="card-body">
          <div class="card-title">Vendas Hoje</div>
          <h3>€ <?= number_format($kpis['vendas_dia'],2,',','.') ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-dashboard text-center">
        <div class="card-body">
          <div class="card-title">Vendas no Mês</div>
          <h3>€ <?= number_format($kpis['vendas_mes'],2,',','.') ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-dashboard text-center">
        <div class="card-body">
          <div class="card-title">Faturamento no Ano</div>
          <h3>€ <?= number_format($kpis['faturamento_ano'],2,',','.') ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfico de Vendas Mensais -->
  <div class="mb-5">
    <h5>📈 Vendas Mensais</h5>
    <canvas id="vendasChart" height="100"></canvas>
    <div class="mt-2">
      <button class="btn btn-sm btn-outline-primary" onclick="atualizarPeriodo('mensal')">Mensal</button>
      <button class="btn btn-sm btn-outline-primary" onclick="atualizarPeriodo('semestral')">Semestre</button>
      <button class="btn btn-sm btn-outline-primary" onclick="atualizarPeriodo('anual')">Anual</button>
    </div>
  </div>

  <!-- Filtros avançados (simulados para manutenção) -->
  <form action="<?= BASE_URL ?>manutencao" method="get" class="row g-3 mb-4">
    <div class="col-md-3"><input type="date" name="de" class="form-control" disabled></div>
    <div class="col-md-3"><input type="date" name="ate" class="form-control" disabled></div>
    <div class="col-md-3">
      <input type="text" name="cliente" class="form-control" placeholder="Cliente / E-mail" disabled>
    </div>
    <div class="col-md-3 d-grid">
      <button type="submit" class="btn btn-outline-secondary">🔧 Em Manutenção</button>
    </div>
  </form>

  <!-- Lista Detalhada de Pedidos -->
  <h5 class="mb-3">📋 Lista de Pedidos</h5>
  <div class="table-responsive shadow-sm mb-5">
    <table class="table table-hover align-middle">
      <thead class="table-success">
        <tr>
          <th>#</th><th>Cliente</th><th>Data/Hora</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($pedidos as $p): ?>
        <tr>
          <td>#<?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['cliente_nome']) ?><br><small><?= htmlspecialchars($p['cliente_email']) ?></small></td>
          <td><?= date('d/m/Y H:i',strtotime($p['data_hora'])) ?></td>
          <td>€ <?= number_format($p['total'] - $p['desconto'],2,',','.') ?> <br><small>Desc €<?= number_format($p['desconto'],2,',','.') ?></small></td>
          <td><?= htmlspecialchars($p['pagamento']) ?></td>
          <td>
            <span class="badge badge-status <?= $p['status_pag']==='Pago'?'bg-success':($p['status_pag']==='Pendente'?'bg-warning':'bg-secondary') ?>">
              <?= htmlspecialchars($p['status_pag']) ?>
            </span>
            <br>
            <small><?= htmlspecialchars($p['status_ent']) ?></small>
          </td>
          <td>
            <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-primary">Ver</a>
            <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-warning">Status</a>
            <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-danger">Cancelar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <footer class="text-center py-4">
    <small class="text-muted">&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</small>
  </footer>
</div>

<script src="<?= $assets ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  // Inicializa gráfico com Chart.js
  const ctx = document.getElementById('vendasChart').getContext('2d');
  let vendasChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($graficoLabels) ?>,
      datasets: [{
        label: '€ Vendas',
        data: <?= json_encode($graficoData) ?>,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } }
    }
  });

  // Simula mudança de período
  function atualizarPeriodo(periodo) {
    alert('Gráfico atualizado para período: ' + periodo.toUpperCase());
    // Aqui você faria uma requisição AJAX para recarregar os dados reais.
  }
</script>
</body>
</html>
