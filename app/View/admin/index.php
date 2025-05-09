<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['msg_sucesso'])): ?>
  <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
    <?= htmlspecialchars($_SESSION['msg_sucesso'], ENT_QUOTES) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
  </div>
<?php 
  unset($_SESSION['msg_sucesso']);
endif;

if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    error_log("[ADM] Acesso negado: usuário não logado.");
    header("Location: " . BASE_URL . "login");
    exit();
}

if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
    error_log("[ADM] Acesso negado: nível de acesso insuficiente.");
    header("Location: " . BASE_URL);
    exit();
}

if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

$assetsPath = BASE_URL . "app/Assets/";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Administrativo - Mercearia</title>
  <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>admin">
    <i class="fas fa-tools me-2"></i> ADM
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav ms-auto align-items-center">
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>admin" title="Dashboard">
          <i class="fas fa-tachometer-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>adminproduto" title="Produtos">
          <i class="fas fa-boxes"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>adminvenda" title="Vendas">
          <i class="fas fa-shopping-cart"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>adminusuario" title="Usuários">
          <i class="fas fa-users"></i>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="gestaoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Gestão">
          <i class="fas fa-cogs"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="gestaoDropdown">
          <li><a class="dropdown-item" href="<?= BASE_URL ?>funcionario"><i class="fas fa-id-badge me-2"></i>Funcionários</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>nivel"><i class="fas fa-layer-group me-2"></i>Níveis</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>fornecedor"><i class="fas fa-truck me-2"></i>Fornecedores</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>adminpromocao"><i class="fas fa-tags me-2"></i>Promoções</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>relatorios"><i class="fas fa-chart-line me-2"></i>Relatórios</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>configuracoes"><i class="fas fa-sliders-h me-2"></i>Configurações</a></li>
        </ul>
      </li>
      <li class="nav-item ms-2">
        <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>sair" title="Sair">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h1 class="text-center">Bem-vindo ao Comando da Mercearia, ADM! 🛒</h1>
  <p class="lead text-center">Você está no controle de tudo por aqui — produtos, promoções e muito mais!</p>
  <p class="lead text-center">Hora de organizar as prateleiras digitais e fazer a mágica acontecer. 💼✨</p>
</div>

  <div class="row text-center mt-4 g-4">
    <div class="col-md-4">
      <div class="alert alert-success">
        <strong>Vendas Hoje:</strong> €1.200
      </div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-primary">
        <strong>Pedidos Pendentes:</strong> 8
      </div>
    </div>
    <div class="col-md-4">
      <div class="alert alert-warning">
        <strong>Estoque Baixo:</strong> 5 itens
      </div>
    </div>
  </div>

  <div class="row mt-4 g-4">
    <?php
    $links = [
      ['Produtos', 'boxes', 'adminproduto', 'primary'],
      ['Vendas', 'shopping-cart', 'adminvenda', 'success'],
      ['Usuários', 'users', 'adminusuario', 'warning'],
      ['Funcionários', 'id-badge', 'funcionario', 'secondary'],
      ['Fornecedores', 'truck', 'fornecedor', 'info'],
      ['Promoções', 'tags', 'adminpromocao', 'primary'],
      ['Níveis', 'layer-group', 'nivel', 'secondary'],
      ['Relatórios', 'chart-line', 'relatorios', 'info'],
      ['Configurações', 'sliders-h', 'configuracoes', 'danger']
    ];

    foreach ($links as [$nome, $icone, $rota, $cor]):
    ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 border-<?= $cor ?>">
          <div class="card-header bg-<?= $cor ?> text-white">
            <i class="fas fa-<?= $icone ?> me-2"></i><?= $nome ?>
          </div>
          <div class="card-body d-flex align-items-end justify-content-center">
            <a href="<?= BASE_URL ?><?= $rota ?>" class="btn btn-<?= $cor ?> w-100">Acessar</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $assetsPath ?>js/modais.js"></script>

<!-- Modal Dinâmico AJAX -->
<div id="ajaxModalContainer">
  <div class="modal fade" id="ajaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ajaxModalTitle">Ação</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
