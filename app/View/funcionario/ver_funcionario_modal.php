<?php
/** @var array $funcionario */
?>
<div class="mb-3">
    <label class="form-label fw-bold">Nome:</label>
    <p><?= htmlspecialchars($funcionario['nome']) ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">E-mail:</label>
    <p><?= htmlspecialchars($funcionario['email'] ?? '-') ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Telefone:</label>
    <p><?= htmlspecialchars($funcionario['telefone'] ?? '-') ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Endereço:</label>
    <p><?= htmlspecialchars($funcionario['endereco'] ?? '-') ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Cargo:</label>
    <p><?= htmlspecialchars($funcionario['cargo'] ?? '-') ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Nível:</label>
    <p><?= htmlspecialchars($funcionario['nivel'] ?? '-') ?></p>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Status:</label>
    <p>
        <span class="badge <?= $funcionario['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
            <?= $funcionario['ativo'] ? 'Ativo' : 'Inativo' ?>
        </span>
    </p>
</div>
<div class="text-end">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
</div>
