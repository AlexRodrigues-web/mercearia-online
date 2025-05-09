<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    error_log("🚫 Acesso negado: usuário não está logado.");
    header("Location: " . BASE_URL . "login");
    exit();
}

$nivelUsuario = $_SESSION['usuario']['nivel_nome'] ?? 'desconhecido';
if (!in_array($nivelUsuario, ['admin', 'funcionario'])) {
    error_log("🚫 Acesso negado: usuário com nível inválido ({$nivelUsuario}).");
    header("Location: " . BASE_URL);
    exit();
}

if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}");
}
$assetsPath = BASE_URL . "/app/Assets/";

error_log("📄 View estoque/index.php carregada.");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estoque - Painel Administrativo</title>
  <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="<?= BASE_URL ?>/admin">Painel Administrativo</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminproduto">Produtos</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminvenda">Vendas</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminusuario">Usuários</a></li>
      <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>/estoque">Estoque</a></li>
      <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="<?= BASE_URL ?>/sair">Sair</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <h1 class="text-center text-info">📦 Gestão de Estoque</h1>
  <p class="lead text-center">Monitore e gerencie seus produtos com controle total.</p>

  <?php
  if (!empty($_SESSION['msg'])) {
      echo $_SESSION['msg'];
      error_log("📣 Mensagem exibida na view: " . strip_tags($_SESSION['msg']));
      unset($_SESSION['msg']);
  }
  ?>

  <table class="table table-hover table-striped">
    <thead class="table-dark">
      <tr>
        <th>Produto</th>
        <th>Categoria</th>
        <th>Quantidade</th>
        <th>Unidade</th>
        <th>Preço</th>
        <th>Status</th>
        <th class="text-center">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($estoques)): ?>
        <?php error_log("📊 Quantidade de itens em estoque: " . count($estoques)); ?>
        <?php foreach ($estoques as $e): ?>
          <?php
            $unit = 'Un';
            if (!empty($e['kilograma']) && $e['kilograma'] > 0) $unit = 'Kg';
            elseif (!empty($e['litro']) && $e['litro'] > 0)    $unit = 'L';
          ?>
          <tr>
            <td><?= htmlspecialchars($e['produto_nome']) ?></td>
            <td><?= htmlspecialchars($e['categoria']) ?></td>
            <td><?= (int)$e['estoque'] ?></td>
            <td><?= $unit ?></td>
            <td>€<?= number_format($e['preco'], 2, ',', '.') ?></td>
            <td>
              <span class="badge <?= $e['estoque'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                <?= $e['estoque'] > 0 ? 'Disponível' : 'Esgotado' ?>
              </span>
            </td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center">
                <a href="<?= BASE_URL ?>/estoque/modalEditar?id=<?= $e['id'] ?>"
                   class="btn btn-sm btn-warning open-modal"
                   data-title="Editar Estoque">Editar</a>
                <a href="<?= BASE_URL ?>/estoque/modalEntrada?id=<?= $e['id'] ?>"
                   class="btn btn-sm btn-primary open-modal"
                   data-title="Entrada de Estoque">Entrada</a>
                <button type="button"
                        class="btn btn-sm btn-danger link_remover_estoque"
                        data-id="<?= $e['id'] ?>"
                        data-url="<?= BASE_URL ?>/estoque/remover">
                  Remover
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <?php error_log("⚠️ Nenhum item de estoque encontrado."); ?>
        <tr><td colspan="7" class="text-center">Nenhum produto em estoque encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal AJAX genérico -->
<div class="modal fade" id="ajaxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="ajaxModalTitle" class="modal-title">...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div id="ajaxModalBody" class="modal-body"></div>
    </div>
  </div>
</div>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $assetsPath ?>js/modais.js"></script>
<script src="<?= $assetsPath ?>js/eventos.js"></script>
</body>
</html>
