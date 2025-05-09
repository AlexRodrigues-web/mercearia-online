<?php /** @var array $funcionario */ ?>
<?php
if (!isset($funcionario) || !is_array($funcionario)) {
    echo "<div class='alert alert-danger'>Erro: Funcionário não encontrado.</div>";
    return;
}
?>

<form id="form-excluir-funcionario" method="post" action="<?= BASE_URL ?>funcionario/excluir">
    <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario['id']) ?>">
    <input type="hidden" name="confirmar" value="1">

    <div class="mb-3 text-center">
        <p class="fs-5">
            Tem certeza que deseja excluir o funcionário
            <strong><?= htmlspecialchars($funcionario['nome']) ?></strong>?
        </p>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Confirmar</button>
    </div>
</form>
