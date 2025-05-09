<?php
/** @var array $usuario */
error_log("🧩 Modal de exclusão carregado para usuário ID: " . ($usuario['id'] ?? 'NÃO DEFINIDO'));
?>

<form id="form-excluir-usuario" method="post" action="<?= BASE_URL ?>adminusuario/excluir">
    <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
    <input type="hidden" name="confirmar" value="1">

    <div class="mb-3 text-center">
        <p class="fs-5">
            Tem certeza que deseja excluir o usuário
            <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
            (<?= htmlspecialchars($usuario['usuario_nivel']) ?>)?
        </p>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>
