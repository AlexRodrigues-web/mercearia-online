<?php
session_start();

// Verificação de autenticação do usuário
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = "Você precisa estar logado para acessar esta página.";
    header("Location: /login");
    exit;
}

// Gerar Token CSRF
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Definir BASE_URL dinamicamente para evitar problemas de caminho fixo
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '<?= BASE_URL ?>/app/Assets');
}

// Garantir que as variáveis são arrays válidos
$funcionario = isset($funcionario) && is_array($funcionario) ? $funcionario : null;
$cargos = isset($cargos) && is_array($cargos) ? $cargos : [];
$niveis = isset($niveis) && is_array($niveis) ? $niveis : [];

// Redirecionar se não houver dados do funcionário
if (!$funcionario) {
    $_SESSION['msg'] = "Erro: Dados do funcionário não encontrados.";
    header("Location: /funcionario");
    exit;
}

// Evitar erro de índice indefinido
$id = $funcionario['id'] ?? '';
$nome = $funcionario['nome'] ?? '';
$cargo_id = $funcionario['cargo_id'] ?? '';
$nivel_id = $funcionario['nivel_id'] ?? '';
$ativo = $funcionario['ativo'] ?? 1; // Padrão: Ativo
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário</title>
    
    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Scripts -->
    <script src="<?= htmlspecialchars(BASE_URL . 'bootstrap/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'js/customFuncionario.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
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
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/institucional"><i class="fas fa-file-alt me-2"></i>Páginas</a></li>
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


    <div class="container">
        <h1 class="text-center mt-5">Editar Funcionário</h1>

        <!-- Mensagens de Feedback -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <form action="/funcionario/atualizar" method="POST">
            <!-- Token CSRF -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Nome -->
            <div class="form-group">
                <label for="nome">Nome</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" 
                    required 
                    aria-label="Nome do funcionário">
            </div>

            <!-- Seleção de Cargo -->
            <div class="form-group">
                <label for="cargo_id">Cargo</label>
                <select name="cargo_id" id="cargo_id" class="form-control" required aria-label="Cargo do funcionário">
                    <option value="">Selecione um cargo</option>
                    <?php foreach ($cargos as $cargo): ?>
                        <?php if (isset($cargo['id'], $cargo['nome'])): ?>
                            <option 
                                value="<?= htmlspecialchars($cargo['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                <?= (string) $cargo['id'] === (string) $cargo_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cargo['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seleção de Nível -->
            <div class="form-group">
                <label for="nivel_id">Nível de Acesso</label>
                <select name="nivel_id" id="nivel_id" class="form-control" required aria-label="Nível de acesso do funcionário">
                    <option value="">Selecione um nível</option>
                    <?php foreach ($niveis as $nivel): ?>
                        <?php if (isset($nivel['id'], $nivel['nome'])): ?>
                            <option 
                                value="<?= htmlspecialchars($nivel['id'], ENT_QUOTES, 'UTF-8') ?>" 
                                <?= (string) $nivel['id'] === (string) $nivel_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nivel['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seleção Ativo/Inativo -->
            <div class="form-group">
                <label for="ativo">Status</label>
                <select name="ativo" id="ativo" class="form-control" required aria-label="Status do funcionário">
                    <option value="1" <?= (string) $ativo === '1' ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= (string) $ativo === '0' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>

            <!-- Campo Senha (Opcional) -->
            <div class="form-group">
                <label for="senha">Redefinir Senha (opcional)</label>
                <input 
                    type="password" 
                    name="senha" 
                    id="senha" 
                    class="form-control" 
                    placeholder="Deixe em branco para manter a senha atual" 
                    minlength="8">
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/funcionario" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
