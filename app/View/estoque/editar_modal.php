<?php
if (!isset($estoque)) {
    error_log("❌ editar_modal.php: Estoque não definido.");
    exit('Produto não encontrado.');
}

error_log("🧩 editar_modal.php carregado para produto ID {$estoque['id']}");
?>

<form id="formEditarEstoque" method="post">
    <input type="hidden" name="id" value="<?= (int)($estoque['id'] ?? 0) ?>">
    <input type="hidden" name="_csrf" value="<?= $_SESSION['_csrf'] ?? '' ?>">

    <div class="mb-3">
        <label class="form-label fw-bold">Produto</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($estoque['produto_nome'] ?? '') ?>" disabled>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold" for="preco">Preço de venda (€)</label>
        <input type="number" step="0.01" name="preco" id="preco" class="form-control"
               value="<?= htmlspecialchars($estoque['preco'] ?? '0.00') ?>" required>
    </div>

    <div class="text-end">
    <button type="button" class="btn btn-success" id="btnSalvarEdicaoEstoque">Salvar</button>
    </div>
</form>

<script>
document.getElementById('formEditarEstoque')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const dados = new FormData(form);

    console.log("📤 Enviando dados do formulário de edição:");
    for (const [chave, valor] of dados.entries()) {
        console.log(`  - ${chave}: ${valor}`);
    }

    try {
        const resposta = await fetch('<?= BASE_URL ?>estoque/atualizar', {
            method: 'POST',
            body: dados,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const resultado = await resposta.json();
        console.log("📬 Resposta recebida:", resultado);

        alert(resultado.message ?? 'Resposta recebida.');

        if (resultado.success || resultado.status) {
            location.reload();
        }

    } catch (err) {
        console.error('❌ Erro ao atualizar produto:', err);
        alert('Erro ao enviar os dados. Tente novamente.');
    }
});
</script>
