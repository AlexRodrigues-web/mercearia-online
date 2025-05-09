(function () {
    "use strict";

    console.log("modais.js carregado corretamente");

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.open-modal');
        if (!trigger) return;
        e.preventDefault();

        const url = trigger.getAttribute('href');
        if (!url) {
            console.error('URL não encontrada no botão/link.');
            Swal.fire('Erro', 'Endereço do modal inválido.', 'error');
            return;
        }

        console.log("Abrindo modal com URL:", url);

        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) throw new Error(`Erro HTTP ${response.status}`);
            return response.text();
        })
        .then(html => {
            console.log("Conteúdo do modal carregado");
            document.getElementById('ajaxModalBody').innerHTML = html;
            document.getElementById('ajaxModalTitle').textContent = trigger.dataset.title || 'Ação';
            new bootstrap.Modal(document.getElementById('ajaxModal')).show();
        })
        .catch(error => {
            console.error('Erro ao abrir modal:', error);
            Swal.fire('Erro', 'Não foi possível carregar a janela.', 'error');
        });
    });

    document.addEventListener('submit', async function (e) {
        const form = e.target;

        // Ignora o formulário de novo usuário 
        if (form.id === 'formNovoUsuario' || form.id === 'formEditarUsuario') return;

        // Entrada de Estoque
        if (form.id === 'formEntradaEstoque') {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const resp = await fetch(`${BASE_URL}estoque/entrada`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();
                if (data.success) {
                    Swal.fire('Sucesso', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Erro', data.message, 'error');
                }
            } catch (err) {
                console.error('Erro na entrada de estoque:', err);
                Swal.fire('Erro', 'Erro ao enviar os dados. Tente novamente.', 'error');
            }
        }

        // Edição de Estoque
        if (form.id === 'formEditarEstoque') {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const resp = await fetch(`${BASE_URL}estoque/atualizar`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();
                if (data.success) {
                    Swal.fire('Sucesso', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Erro', data.message, 'error');
                }
            } catch (err) {
                console.error('Erro ao atualizar estoque:', err);
                Swal.fire('Erro', 'Erro ao enviar os dados. Tente novamente.', 'error');
            }
        }
    });

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.link_remover_estoque');
        if (!btn) return;
        e.preventDefault();

        const id = btn.getAttribute('data-id');
        const url = btn.getAttribute('data-url');
        if (!id || !url) {
            Swal.fire('Erro', 'Produto inválido.', 'error');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Remover Produto',
            text: 'Tem certeza que deseja remover este item do estoque?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        });
        if (!confirm.isConfirmed) return;

        const formData = new FormData();
        formData.append('id', id);
        try {
            const resp = await fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (data.success) {
                Swal.fire('Removido', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Erro', data.message, 'error');
            }
        } catch (err) {
            console.error('Erro na remoção:', err);
            Swal.fire('Erro', 'Erro ao processar a requisição.', 'error');
        }
    });

})();
