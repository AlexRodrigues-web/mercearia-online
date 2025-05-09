<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

if (
    empty($_SESSION['usuario']['logado']) ||
    $_SESSION['usuario']['logado'] !== true ||
    !in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])
) {
    header("Location: " . BASE_URL . "login");
    exit();
}

$assetsPath = BASE_URL . "app/Assets/";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestão de Usuários - Painel Administrativo</title>
  <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css">
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
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminproduto" title="Produtos"><i class="fas fa-boxes"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminvenda" title="Vendas"><i class="fas fa-shopping-cart"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminusuario" title="Usuários"><i class="fas fa-users"></i></a></li>
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

  <!-- ✅ Mensagem pós-exclusão -->
  <?php if (!empty($_SESSION['msg_sucesso'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        Swal.fire('Sucesso', '<?= $_SESSION['msg_sucesso'] ?>', 'success');
      });
    </script>
    <?php unset($_SESSION['msg_sucesso']); ?>
  <?php elseif (!empty($_SESSION['msg_erro'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        Swal.fire('Erro', '<?= $_SESSION['msg_erro'] ?>', 'error');
      });
    </script>
    <?php unset($_SESSION['msg_erro']); ?>
  <?php endif; ?>

  <h1 class="mb-4 text-success"><i class="fas fa-users me-2"></i>Gestão de Usuários</h1>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">← Voltar ao Painel</a>
    <a href="<?= BASE_URL ?>adminusuario/novo"
       class="btn btn-success open-modal"
       data-title="Novo Usuário">+ Novo Usuário</a>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Nível</th>
          <th>Cadastro</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($usuarios)): ?>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['usuario_nivel'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= date('d/m/Y H:i', strtotime($u['dt_registro'])) ?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="<?= BASE_URL ?>adminusuario/ver?id=<?= $u['id'] ?>" class="btn btn-sm btn-info open-modal" data-title="Ver Usuário">Ver</a>
                  <a href="<?= BASE_URL ?>adminusuario/editar?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning open-modal" data-title="Editar Usuário">Editar</a>
                  <a href="<?= BASE_URL ?>adminusuario/bloquear?id=<?= $u['id'] ?>" class="btn btn-sm btn-secondary open-modal" data-title="Bloquear Usuário">Bloquear</a>
                  <a href="<?= BASE_URL ?>adminusuario/excluir?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger open-modal" data-title="Excluir Usuário">Excluir</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Nenhum usuário encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="ajaxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ajaxModalTitle">Carregando...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="ajaxModalBody"></div>
    </div>
  </div>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>app/Assets/js/modais.js"></script>
<script src="<?= BASE_URL ?>app/Assets/js/eventos.js"></script>
</body>
</html>
