<?php
session_start();

// Verificar autenticação do usuário
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Você precisa estar logado para acessar esta página.');
    header("Location: /login");
    exit;
}

// Definir BASE_URL apenas se ainda não estiver definida
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '<?= BASE_URL ?>/app/Assets');
}

// Gerar Token CSRF caso não exista
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Verificação segura dos dados do estoque (permitindo valores como "0")
if (!isset($estoque) || !is_array($estoque) || !isset($estoque['id'], $estoque['produto_nome'], $estoque['quantidade'])) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Erro ao carregar os dados do estoque.');
    header("Location: /estoque");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estoque</title>
    
    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>bootstrap/css/bootstrap.min.css">
    
    <!-- Scripts -->
    <script src="<?= BASE_URL ?>app/bootstrap/js/bootstrap.min.js" defer></script>
    <script src="<?= BASE_URL ?>js/customEstoque.js" defer></script>
</head>
<body>

    <!-- ✅ Navbar ADM -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= BASE_URL ?>/admin">Painel Administrativo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminproduto">Produtos</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminvenda">Vendas</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/adminusuario">Usuários</a></li>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="<?= BASE_URL ?>/sair">Sair</a></li>
        </ul>
    </div>
</nav>

    <div class="container">
        <h1 class="text-center mt-5">Editar Estoque</h1>

        <!-- Exibição de mensagens de alerta -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= $_SESSION['msg'] ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <form action="/estoque/atualizar" method="POST">
            <!-- Token CSRF -->
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            
            <!-- Campo oculto com o ID do produto -->
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $estoque['id'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Campo Produto (Desabilitado) -->
            <div class="form-group">
                <label for="produto_nome">Produto</label>
                <input 
                    type="text" 
                    id="produto_nome" 
                    class="form-control" 
                    value="<?= htmlspecialchars((string) $estoque['produto_nome'], ENT_QUOTES, 'UTF-8') ?>" 
                    disabled>
            </div>

            <!-- Campo Quantidade -->
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input 
                    type="number" 
                    name="quantidade" 
                    id="quantidade" 
                    class="form-control" 
                    value="<?= htmlspecialchars((string) $estoque['quantidade'], ENT_QUOTES, 'UTF-8') ?>" 
                    min="0" 
                    required>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="/estoque" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
