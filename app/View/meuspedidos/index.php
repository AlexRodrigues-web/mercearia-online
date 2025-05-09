<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', rtrim("{$protocol}://{$host}{$scriptDir}", '/') . '/');
}

$assetsPath = BASE_URL . "app/Assets/";
$pedidos = $dados['pedidos'] ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos | Mercearia</title>
    <link rel="stylesheet" href="<?= $assetsPath ?>bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia.css">
    <link rel="stylesheet" href="<?= $assetsPath ?>css/mercearia-custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container py-5">
    <h1 class="text-center text-success mb-4"><i class="fas fa-box-open"></i> Meus Pedidos</h1>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info text-center shadow-sm">
            <i class="fas fa-info-circle me-2"></i> Você ainda não fez nenhum pedido.
        </div>
    <?php else: ?>
        <div class="alert alert-success text-center fw-semibold shadow-sm">
            ✅ Você já fez <strong><?= count($pedidos) ?></strong> pedido(s).
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover text-center align-middle shadow-sm">
                <thead class="table-success">
                    <tr>
                        <th># Pedido</th>
                        <th>Data</th>
                        <th>Total (€)</th>
                        <th>Status</th>
                        <th>Pagamento</th>
                        <th>Entrega</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($pedido['numero']) ?></td>
                            <td><?= htmlspecialchars($pedido['data']) ?></td>
                            <td>€ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($pedido['status']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($pedido['metodo_pagamento'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($pedido['metodo_entrega'] ?? '—') ?></td>
                            <td class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                                <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-redo"></i>
                                </a>
                                <?php if ($pedido['status'] === 'Processando'): ?>
                                    <a href="<?= BASE_URL ?>manutencao" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>produtos" class="btn btn-outline-primary me-2">
            <i class="fas fa-shopping-cart me-1"></i> Continuar Comprando
        </a>
        <a href="<?= BASE_URL ?>perfil" class="btn btn-outline-secondary">
            <i class="fas fa-user me-1"></i> Voltar ao Perfil
        </a>
    </div>
</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>

<script src="<?= $assetsPath ?>bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
