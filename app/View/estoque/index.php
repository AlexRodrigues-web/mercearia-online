<?php
session_start(); // Sempre no topo do arquivo

include_once '../include/mensagens.php';

// ============================
// 🔒 Verificação de Autenticação
// ============================
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['msg'] = (new \App\Model\Alerta())->alertaFalha('Você precisa estar logado para acessar esta página.');
    header("Location: /login");
    exit;
}

// ============================
// 🌍 Definir BASE_URL Dinâmica
// ============================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Caminho ajustado corretamente
    define('BASE_URL', "{$protocol}://{$host}{$scriptDir}/app/Assets/");
}

// ============================
// 🛡️ Garante que `$estoques` seja um array válido
// ============================
$estoques = isset($estoques) && is_array($estoques) ? $estoques : [];

// ============================
// 🔑 Gerar Token CSRF Caso Não Exista
// ============================
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estoque</title>
    
    <!-- Links CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'bootstrap/css/bootstrap.min.css', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Scripts -->
    <script src="<?= htmlspecialchars(BASE_URL . 'bootstrap/js/bootstrap.min.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <script src="<?= htmlspecialchars(BASE_URL . 'js/customEstoque.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Gestão de Estoque</h1>

        <!-- ✅ Exibição de Mensagens de Alerta -->
        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars(is_array($_SESSION['msg']) ? implode('<br>', $_SESSION['msg']) : $_SESSION['msg'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/estoque/novo" class="btn btn-success btn-sm">
                Adicionar Novo Produto
            </a>
        </div>

        <!-- ✅ Tabela de Estoques -->
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estoques)): ?>
                    <?php foreach ($estoques as $estoque): ?>
                        <?php
                        // ✅ Garantindo que as chaves necessárias existam
                        if (!isset($estoque['id'], $estoque['produto_nome'], $estoque['quantidade'])) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $estoque['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $estoque['produto_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $estoque['quantidade'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="/estoque/editar?id=<?= urlencode($estoque['id']) ?>" 
                                   class="btn btn-primary btn-sm">Editar</a>

                                <!-- ✅ Formulário para Exclusão com Proteção CSRF -->
                                <form action="/estoque/excluir" method="POST" style="display:inline;" 
                                      onsubmit="return confirm('Deseja realmente excluir este item do estoque?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $estoque['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Nenhum produto cadastrado no estoque.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
