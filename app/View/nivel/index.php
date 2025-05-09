<?php
if (!isset($_SESSION['usuario']['logado']) || $_SESSION['usuario']['logado'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit();
}
if (!in_array($_SESSION['usuario']['nivel_nome'] ?? '', ['admin', 'funcionario'])) {
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
$niveis     = $niveis ?? [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Níveis - Painel Administrativo</title>
    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
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

<div class="container mt-5">
    <h1 class="text-center text-primary">👥 Gestão de Níveis</h1>
    <p class="lead text-center">Gerencie os níveis de acesso dos usuários do sistema.</p>

    <?php if (!empty($_SESSION['msg_sucesso'])): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($_SESSION['msg_sucesso']) ?>
        </div>
        <?php unset($_SESSION['msg_sucesso']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['msg_erro'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_SESSION['msg_erro']) ?>
        </div>
        <?php unset($_SESSION['msg_erro']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['msg'])): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($_SESSION['msg']) ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= BASE_URL ?>/nivel/novo" class="btn btn-success">+ Novo Nível</a>
        <input type="text" id="filtroBuscaNivel" class="form-control w-50" placeholder="Buscar nível por nome...">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="tabelaNiveis">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Usuários</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($niveis): ?>
                <?php foreach ($niveis as $nivel): ?>
                    <?php
                        $badgeClass = $nivel['status'] === 'ativo' ? 'bg-success' : 'bg-secondary';
                        $statusLabel = ucfirst($nivel['status']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars((string)$nivel['id'], ENT_QUOTES) ?></td>
                        <td class="nome-nivel"><?= htmlspecialchars($nivel['nome'], ENT_QUOTES) ?></td>
                        <td><?= rand(0, 8) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= BASE_URL ?>/nivel/editar?id=<?= $nivel['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <form action="<?= BASE_URL ?>/nivel/excluir" method="POST" onsubmit="return confirm('Deseja realmente excluir este nível?');">
                                    <input type="hidden" name="id" value="<?= $nivel['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhum nível cadastrado.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/admin" class="btn btn-secondary">⬅ Voltar ao Painel Administrativo</a>
    </div>
</div>

<footer class="text-center mt-5 mb-3">
    <p class="text-muted">&copy; <?= date('Y') ?> Mercearia. Todos os direitos reservados.</p>
</footer>

<!-- Script da busca -->
<script>
    document.getElementById('filtroBuscaNivel').addEventListener('keyup', function () {
        const termo = this.value.toLowerCase().trim();
        const linhas = document.querySelectorAll('#tabelaNiveis tbody tr');

        linhas.forEach(linha => {
            const nome = linha.querySelector('.nome-nivel')?.textContent.toLowerCase() || '';
            linha.style.display = nome.includes(termo) ? '' : 'none';
        });
    });
</script>

</body>
</html>
