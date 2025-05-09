<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container py-5 text-center fade-in">
    <h1 class="text-success mb-4"><i class="fas fa-check-circle"></i> Pedido Finalizado com Sucesso!</h1>
    <p class="lead">Obrigado pela sua compra! Estamos preparando tudo com muito carinho ❤️</p>

    <!-- Etapas visuais -->
    <div class="row justify-content-center my-5">
        <?php
        $etapas = [
            ['icon' => 'clipboard-check', 'label' => 'Pedido Recebido'],
            ['icon' => 'box-open', 'label' => 'Separando Itens'],
            ['icon' => 'truck', 'label' => 'Em Transporte'],
            ['icon' => 'smile-beam', 'label' => 'Entregue']
        ];
        foreach ($etapas as $i => $etapa): ?>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-3 mb-4">
                    <i class="fas fa-<?= $etapa['icon'] ?> fa-2x text-success mb-2"></i>
                    <p class="fw-semibold mb-0"><?= $etapa['label'] ?></p>
                    <?php if ($i === 0): ?>
                        <span class="badge bg-success mt-2">Atual</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Dados do pedido -->
    <div class="bg-light p-4 rounded shadow-sm mx-auto" style="max-width: 500px;">
        <h5 class="mb-3">Resumo do Pedido</h5>
        <p class="mb-1">💰 <strong>Total:</strong> €<?= number_format($pedido['total'], 2, ',', '.') ?></p>
        <p class="mb-1">💳 <strong>Pagamento:</strong> <?= ucfirst($pedido['metodo']) ?></p>
        <?php if (!empty($pedido['numero'])): ?>
            <p class="mb-0">📦 <strong>Nº do Pedido:</strong> <?= $pedido['numero'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Botões -->
    <div class="mt-5">
        <a href="<?= BASE_URL ?>produtos" class="btn btn-success btn-lg px-4"><i class="fas fa-store"></i> Continuar Comprando</a>
    </div>
</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
