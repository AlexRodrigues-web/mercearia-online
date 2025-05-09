<?php
// ⚠️ A sessão já está iniciada no bootstrap do projeto

if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: " . BASE_URL . "login");
    exit;
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
    header("Location: " . BASE_URL);
    exit;
}

if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/");
}

$assetsPath    = BASE_URL . "app/Assets/";
$cssBootstrap  = htmlspecialchars($assetsPath . "css/bootstrap.min.css", ENT_QUOTES);
$jsBootstrap   = htmlspecialchars($assetsPath . "js/bootstrap.min.js", ENT_QUOTES);
$customJsNivel = htmlspecialchars($assetsPath . "js/customNivel.js", ENT_QUOTES);

$csrfToken = $_SESSION['csrf_token'] ?? '';
$nivel     = $nivel ?? ['id' => '', 'nome' => '', 'status' => 'ativo'];

$id     = htmlspecialchars((string)($nivel['id'] ?? ''), ENT_QUOTES);
$nome   = htmlspecialchars((string)($nivel['nome'] ?? ''), ENT_QUOTES);
$status = in_array(($nivel['status'] ?? ''), ['ativo', 'inativo'], true)
             ? $nivel['status']
             : 'ativo';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Nível</title>
  <link rel="stylesheet" href="<?= $cssBootstrap ?>">
  <script src="<?= $jsBootstrap ?>" defer></script>
  <script src="<?= $customJsNivel ?>" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>admin">Painel ADM</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- links de navegação -->
      </ul>
    </div>
  </div>
</nav>

<main class="container mt-5">
  <h2 class="text-center text-primary mb-4">Editar Nível</h2>

  <div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= BASE_URL ?>/nivel" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
  </div>


  <!-- SUCESSO -->
  <?php if (!empty($_SESSION['msg_sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
      <?= htmlspecialchars($_SESSION['msg_sucesso'], ENT_QUOTES) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php unset($_SESSION['msg_sucesso']); ?>
  <?php endif; ?>

  <!-- INFORMAÇÃO -->
  <?php if (!empty($_SESSION['msg'])): ?>
    <div class="alert alert-info text-center" role="alert">
      <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES) ?>
    </div>
    <?php unset($_SESSION['msg']); ?>
  <?php endif; ?>

  <!-- AVISO (p. ex. nenhuma alteração detectada) -->
  <?php if (!empty($_SESSION['msg_info'])): ?>
    <div class="alert alert-warning text-center" role="alert">
      <?= htmlspecialchars($_SESSION['msg_info'], ENT_QUOTES) ?>
    </div>
    <?php unset($_SESSION['msg_info']); ?>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>nivel/atualizar" method="POST" class="shadow p-4 bg-white rounded">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="mb-3">
      <label for="nome" class="form-label">Nome do Nível</label>
      <input
        type="text"
        id="nome"
        name="nome"
        class="form-control"
        value="<?= $nome ?>"
        required
        maxlength="7"
      >
      <div class="form-text">Máximo 7 caracteres.</div>
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select id="status" name="status" class="form-select" required>
        <option value="ativo" <?= $status === 'ativo' ? 'selected' : '' ?>>Ativo</option>
        <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</option>
      </select>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
      <div>
        <button type="submit" class="btn btn-success">Salvar alterações</button>
        <a href="<?= BASE_URL ?>nivel" class="btn btn-secondary">Cancelar</a>
      </div>
      <a href="<?= BASE_URL ?>nivel/permissoes?id=<?= $id ?>" class="btn btn-outline-info">
        Permissões
      </a>
    </div>
  </form>
</main>

</body>
</html>
