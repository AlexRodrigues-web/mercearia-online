<?php /** @var array|null $usuario */ ?>

<?php if (empty($usuario)): ?>
    <div class="alert alert-warning">
        Usuário não encontrado.
    </div>
<?php else: ?>
    <div class="mb-3">
        <label class="form-label fw-bold">Nome:</label>
        <p><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Email:</label>
        <p><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Nível:</label>
        <p><?= htmlspecialchars($usuario['usuario_nivel'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
    </div>
<?php endif; ?>
