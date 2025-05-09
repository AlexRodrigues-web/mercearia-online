(function ($) {
    "use strict";

    console.log("eventos.js carregado corretamente");

    let enviandoFormulario = false;

    function escaparHTML(texto) {
        const div = document.createElement("div");
        div.textContent = texto;
        return div.innerHTML;
    }

    function showAlert(type, title, message) {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    function formatCurrency(value) {
        const num = parseFloat(value.replace(/[^\d,.-]/g, '').replace(',', '.'));
        return isNaN(num) ? 0 : num;
    }

    function enviarFormularioAjax(form, url, successRedirect = null) {
        if (enviandoFormulario) return;
        enviandoFormulario = true;

        const formData = new FormData(form);
        console.log("Enviando para:", url);
        console.log("Dados enviados:", Object.fromEntries(formData.entries()));

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const ct = response.headers.get('Content-Type') || '';
            if (!ct.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error(`Resposta inválida do servidor: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log("Resposta recebida:", data);
            if (data.success) {
                showAlert('success', 'Sucesso', data.message || 'Operação realizada com sucesso!');

                // Fecha automaticamente o modal, se existir
                const modalEl = form.closest('.modal');
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }

                setTimeout(() => {
                    window.location.href = successRedirect || window.location.href;
                }, 1500);
            } else {
                showAlert('error', 'Erro', data.message || 'Ocorreu um erro na operação.');
            }
        })
        .catch(error => {
            console.error("Erro AJAX:", error);
            const msg = error.message.startsWith('Resposta inválida')
                      ? error.message
                      : 'Erro de comunicação com o servidor.';
            showAlert('error', 'Erro', msg);
        })
        .finally(() => {
            enviandoFormulario = false;
        });
    }

    // Usuário (adminusuario)

    $('body').on('click', '#btnSalvarUsuario', function (e) {
        e.preventDefault();    
        const form = document.getElementById('formEditarUsuario');
        enviarFormularioAjax(form, `${window.BASE_URL}adminusuario/salvar`, `${window.BASE_URL}adminusuario`);
    });

    $('body').on('click', '#btnSalvarNovoUsuario', function (e) {
        e.preventDefault();    
        const form = document.getElementById('formNovoUsuario');
        enviarFormularioAjax(form, `${window.BASE_URL}adminusuario/salvar`, `${window.BASE_URL}adminusuario`);
    });

    $('body').on('click', '#btnConfirmarBloqueio', function () {
        const form = document.getElementById('formBloquearUsuario');
        enviarFormularioAjax(form, `${window.BASE_URL}adminusuario/bloquear`, `${window.BASE_URL}adminusuario`);
    });

    // Funcionário (novo/editar)

    $('body').on('click', '#btnSalvar', function (e) {
        e.preventDefault();   
        const form = document.getElementById('form-salvar-funcionario');
        if (!form) {
            showAlert('error', 'Erro', 'Formulário de funcionário não encontrado.');
            return;
        }
        const url = `${window.BASE_URL}funcionario/salvar`;
        enviarFormularioAjax(form, url, `${window.BASE_URL}funcionario`);
    });

    // Carrinho
    window.abrirModalCarrinho = function (produto) {
        $('#produto_id').val(produto.id);
        $('#quantidade').val(1);
        $('#unidade').val('unidade');
        new bootstrap.Modal(document.getElementById('modalCarrinho')).show();
    };

    let enviandoCarrinho = false;
    $('#formAdicionarCarrinho').on('submit', function (e) {
        e.preventDefault();
        if (enviandoCarrinho) return;
        enviandoCarrinho = true;

        const formData = new FormData(this);
        const url = `${window.BASE_URL}carrinho/adicionarViaAjax`;

        fetch(url, { method: 'POST', body: formData, credentials: 'same-origin' })
            .then(resp => resp.json())
            .then(data => {
                if (data.sucesso) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCarrinho')).hide();
                    new bootstrap.Toast(document.getElementById('toastCarrinho')).show();
                } else {
                    showAlert('error', 'Erro', data.mensagem || 'Não foi possível adicionar ao carrinho.');
                }
            })
            .catch(err => {
                console.error("ERRO:", err);
                showAlert('error', 'Erro', 'Erro de comunicação com o servidor.');
            })
            .finally(() => { enviandoCarrinho = false; });
    });

    $('#btn-menu').click(() => $('#btn-menu').toggleClass("border-btn-menu"));

    [
        { btn: '#detalhes-produtos', tgt: '.card-produto' },
        { btn: '#detalhes-fornecedor', tgt: '.card-fornecedor' },
        { btn: '#detalhes-funcionario', tgt: '.card-funcionario' },
        { btn: '#detalhes-caixa', tgt: '.card-caixa' },
    ].forEach(({ btn, tgt }) =>
        $(btn).click(() => {
            $(tgt).toggleClass('d-none d-block');
            $(btn).text($(tgt).hasClass('d-none') ? 'Detalhes' : 'Ocultar');
        })
    );

    $('body').on('click', '.btn-entrada-estoque', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (!id) {
            showAlert('error', 'Erro', 'Produto inválido.');
            return;
        }

        Swal.fire({
            title: 'Adicionar Entrada',
            input: 'number',
            inputLabel: 'Quantidade de entrada',
            inputAttributes: { min: 1, step: 1 },
            showCancelButton: true,
            confirmButtonText: 'Adicionar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value || value <= 0) {
                    return 'Informe uma quantidade válida.';
                }
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('quantidade', result.value);

            fetch(`${window.BASE_URL}estoque/entrada`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Sucesso', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Erro', data.message);
                }
            })
            .catch(err => {
                console.error("Erro na entrada:", err);
                showAlert('error', 'Erro', 'Erro de comunicação com o servidor.');
            });
        });
    });

    $('body').on('click', '.link_remover_estoque', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const url = $(this).data('url');

        if (!id || !url) {
            showAlert('error', 'Erro', 'Produto inválido.');
            return;
        }

        Swal.fire({
            title: 'Remover Produto',
            text: 'Tem certeza que deseja remover este item do estoque?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((res) => {
            if (!res.isConfirmed) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('confirmar', true);

            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Removido', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Erro', data.message);
                }
            })
            .catch(err => {
                console.error("Erro na remoção:", err);
                showAlert('error', 'Erro', 'Erro de comunicação com o servidor.');
            });
        });
    });

    document.querySelectorAll('.link_remover_estoque').forEach(botao => {
        botao.addEventListener('click', async () => {
            const id = botao.getAttribute('data-id');
            const url = botao.getAttribute('data-url');

            if (!confirm('Tem certeza que deseja remover este item do estoque?')) return;

            const formData = new FormData();
            formData.append('id', id);

            const resposta = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const resultado = await resposta.json();
            alert(resultado.mensagem);

            if (resultado.status) {
                location.reload();
            }
        });
    });

    $('body').on('click', '#btnImportarCsv', function () {
        Swal.fire({
            title: 'Importar Arquivo CSV',
            html: `<input type="file" id="inputCsvFile" class="form-control" accept=".csv">`,
            showCancelButton: true,
            confirmButtonText: 'Importar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const fileInput = Swal.getPopup().querySelector('#inputCsvFile');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage(`Você precisa selecionar um arquivo CSV.`);
                }
                return fileInput.files[0];
            }
        }).then((result) => {
            if (!result.isConfirmed || !result.value) return;

            const formData = new FormData();
            formData.append('csv', result.value);

            fetch(`${window.BASE_URL}estoque/importarCsv`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Importado', data.message || 'Importação realizada com sucesso.');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Erro', data.message || 'Erro ao importar.');
                }
            })
            .catch(err => {
                console.error("Erro ao importar CSV:", err);
                showAlert('error', 'Erro', 'Erro de comunicação.');
            });
        });
    });

    $('body').on('click', '#btnSalvarEdicaoEstoque', function () {
        const form = document.getElementById('form-editar-estoque');
        if (!form) {
            showAlert('error', 'Erro', 'Formulário de edição não encontrado.');
            return;
        }
        const url = `${window.BASE_URL}estoque/salvarEdicao`;
        enviarFormularioAjax(form, url, `${window.BASE_URL}estoque`);
    });

    $('body').on('click', '#btnSalvarEntradaEstoque', function () {
        const form = document.getElementById('form-entrada-estoque');
        if (!form) {
            showAlert('error', 'Erro', 'Formulário de entrada não encontrado.');
            return;
        }
        const url = `${window.BASE_URL}estoque/salvarEntrada`;
        enviarFormularioAjax(form, url, `${window.BASE_URL}estoque`);
    });

    $('body').on('submit', '#form-excluir-funcionario', function (e) {
        e.preventDefault();

        if (enviandoFormulario) return;
        enviandoFormulario = true;

        const form = this;
        const formData = new FormData(form);
        formData.append('confirmar', true);

        fetch(`${window.BASE_URL}funcionario/excluir`, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Sucesso', data.message);
                const modalEl = form.closest('.modal');
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', 'Erro', data.message);
            }
        })
        .catch(error => {
            console.error("Erro ao excluir funcionário:", error);
            showAlert('error', 'Erro', 'Erro de comunicação com o servidor.');
        })
        .finally(() => {
            enviandoFormulario = false;
        });
    });

    // Funcionário - Abertura do modal de exclusão
    $('body').on('click', '.link_excluir', function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        const url = $(this).data('url');

        if (!id || !url) {
            showAlert('error', 'Erro', 'Funcionário inválido.');
            return;
        }

        fetch(`${url}?id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(resp => resp.text())
        .then(html => {
            $('#ajaxModalTitle').text('Excluir Funcionário');
            $('#ajaxModalBody').html(html);
            new bootstrap.Modal(document.getElementById('ajaxModal')).show();
        })
        .catch(err => {
            console.error("Erro ao carregar modal de exclusão:", err);
            showAlert('error', 'Erro', 'Erro ao carregar a janela de confirmação.');
        });
    });

})(jQuery);
