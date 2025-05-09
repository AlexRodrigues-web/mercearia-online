<?php /** @var array|null $usuario */ ?>

<?php if (empty($usuario)): ?>
  <div class="alert alert-warning text-center">
    Usuário não encontrado.
  </div>
<?php else: ?>
  <?php error_log("🔒 Modal de bloqueio carregado para usuário ID: " . ($usuario['id'] ?? 'NÃO DEFINIDO')); ?>
  <form
    id="formBloquearUsuario"
    action="<?= BASE_URL ?>adminusuario/bloquear"
    method="POST"
  >
    <input
      type="hidden"
      name="id"
      value="<?= htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8') ?>"
    >

    <div class="mb-3 text-center">
      <p class="fs-5">
        Tem certeza que deseja bloquear o usuário
        <strong><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></strong>?
      </p>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <button
        type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal"
      >
        Cancelar
      </button>
      <button
        type="submit"
        class="btn btn-danger"
      >
        Confirmar Bloqueio
      </button>
    </div>
  </form>
<?php endif; ?>
