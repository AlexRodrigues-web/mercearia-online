<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// BASE_URL dinâmica
if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

// Autenticação
if (
    empty($_SESSION['usuario']['logado']) ||
    $_SESSION['usuario']['logado'] !== true ||
    $_SESSION['usuario']['nivel_nome'] !== 'admin'
) {
    header("Location: " . BASE_URL . "home");
    exit();
}

$assetsPath = BASE_URL . "app/Assets/";
$funcionarios = $funcionarios ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestão de Funcionários</title>
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
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin"><i class="fas fa-tachometer-alt"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminproduto"><i class="fas fa-boxes"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminvenda"><i class="fas fa-shopping-cart"></i></a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminusuario"><i class="fas fa-users"></i></a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="gestaoDropdown" role="button" data-bs-toggle="dropdown"><i class="fas fa-cogs"></i></a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/funcionario"><i class="fas fa-id-badge me-2"></i>Funcionários</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/nivel"><i class="fas fa-layer-group me-2"></i>Níveis</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/estoque"><i class="fas fa-warehouse me-2"></i>Estoque</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/fornecedor"><i class="fas fa-truck me-2"></i>Fornecedores</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/promocao"><i class="fas fa-tags me-2"></i>Promoções</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/relatorios"><i class="fas fa-chart-line me-2"></i>Relatórios</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/configuracoes"><i class="fas fa-sliders-h me-2"></i>Configurações</a></li>
                </ul>
            </li>
            <li class="nav-item ms-2"><a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/sair"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
  <h1 class="mb-4 text-success"><i class="fas fa-id-badge me-2"></i>Gestão de Funcionários</h1>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">← Voltar ao Painel</a>
    <a href="<?= BASE_URL ?>funcionario/novo"
       class="btn btn-success open-modal"
       data-title="Novo Funcionário">
      + Novo Funcionário
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Cargo</th>
          <th>Nível</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($funcionarios)): ?>
          <?php foreach ($funcionarios as $f): ?>
            <tr>
              <td><?= htmlspecialchars($f['id'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($f['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($f['nivel'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <span class="badge <?= $f['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                  <?= $f['ativo'] ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td>
                <div class="d-flex gap-2 flex-wrap">
                  <a href="<?= BASE_URL ?>funcionario/ver?id=<?= $f['id'] ?>"
                     class="btn btn-sm btn-info open-modal"
                     data-title="Ver Funcionário">Ver</a>

                  <a href="<?= BASE_URL ?>funcionario/editar?id=<?= $f['id'] ?>"
                     class="btn btn-sm btn-warning open-modal"
                     data-title="Editar Funcionário">Editar</a>

                  <button class="btn btn-sm btn-danger btn-excluir"
                          data-id="<?= $f['id'] ?>"
                          data-nome="<?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?>">
                    Excluir
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Nenhum funcionário encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Base -->
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalTitle" aria-hidden="true">
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

<!-- Scripts -->
<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>app/Assets/js/modais.js"></script>
<script src="<?= BASE_URL ?>app/Assets/js/eventos.js"></script>

<script>
  $(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    const nome = $(this).data('nome');

    Swal.fire({
      title: 'Tem certeza?',
      html: `Deseja realmente excluir <strong>${nome}</strong>?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sim, excluir',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('<?= BASE_URL ?>funcionario/excluir', { id: id }, function (resposta) {
          if (resposta.success) {
            Swal.fire('Excluído!', resposta.message, 'success').then(() => location.reload());
          } else {
            Swal.fire('Erro', resposta.message, 'error');
          }
        }, 'json').fail(() => {
          Swal.fire('Erro', 'Erro na comunicação com o servidor.', 'error');
        });
      }
    });
  });
</script>

</body>
</html>
