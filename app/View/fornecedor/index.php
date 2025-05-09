<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

$fornecedores = isset($fornecedores) && is_array($fornecedores) ? $fornecedores : [];
$total = count($fornecedores);

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Fornecedores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/Assets/css/mercearia-custom.css">
    <script src="<?= BASE_URL ?>app/Assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>
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

<?php error_log("✅ View fornecedor/index carregada com " . $total . " fornecedores."); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">📦 Gestão de Fornecedores</h1>

    <!-- Mensagens de feedback -->
    <?php if (!empty($_SESSION['msg_sucesso'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['msg_erro'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['msg_info'])): ?>
        <div class="alert alert-info text-center"><?= $_SESSION['msg_info']; unset($_SESSION['msg_info']); ?></div>
    <?php endif; ?>

    <!-- Botões -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        <a href="<?= BASE_URL ?>fornecedor/cadastrar" class="btn btn-success">+ Novo Fornecedor</a>
    </div>

    <!-- Tabela -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>NIPC</th>
                    <th>Data de Registro</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total > 0): ?>
                    <?php foreach ($fornecedores as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['id']) ?></td>
                            <td><?= html_entity_decode($f['nome']) ?></td>
                            <td><?= html_entity_decode($f['nipc']) ?></td>

                            <td><?= !empty($f['dt_registro']) ? date('d/m/Y H:i', strtotime($f['dt_registro'])) : '—' ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>fornecedor/editar?id=<?= $f['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form action="<?= BASE_URL ?>fornecedor/excluir" method="POST" onsubmit="return confirm('Deseja realmente excluir este fornecedor?');" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted">Nenhum fornecedor encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
