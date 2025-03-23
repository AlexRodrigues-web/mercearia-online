(function ($) {
    "use strict";

    // ===============================
    // 🌟 Funções Auxiliares
    // ===============================

    // Escapar HTML para prevenir XSS
    function escaparHTML(texto) {
        const div = document.createElement("div");
        div.textContent = texto; // Substitui innerText para melhor segurança
        return div.innerHTML;
    }

    // Exibir alertas amigáveis com SweetAlert2
    function showAlert(type, title, message) {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    // Validar formulário com mensagens customizadas
    function validateForm(validators, fields) {
        let isValid = true;

        fields.forEach(([id, errorMessage], index) => {
            const field = $(`#${id}`);
            field.removeClass('is-invalid');
            $(`#d-${id}`).remove();

            if (!validators[index]) {
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback" id="d-${id}">${escaparHTML(errorMessage)}</div>`);
                isValid = false;
            }
        });

        return isValid;
    }

    // Formatar valor como moeda (BRL)
    function formatCurrency(value) {
        const num = parseFloat(value.replace(/[^\d,.-]/g, '').replace(',', '.'));
        return isNaN(num) ? 0 : num;
    }

    // ===============================
    // 📂 Interatividade com Botões
    // ===============================

    // Efeito no botão do menu
    $('#btn-menu').click(function () {
        $(this).toggleClass("border-btn-menu");
    });

    // Exibir/ocultar detalhes de seções
    const sections = [
        { button: '#detalhes-produtos', target: '.card-produto' },
        { button: '#detalhes-fornecedor', target: '.card-fornecedor' },
        { button: '#detalhes-funcionario', target: '.card-funcionario' },
        { button: '#detalhes-caixa', target: '.card-caixa' },
    ];

    sections.forEach(({ button, target }) => {
        $(button).click(() => {
            $(target).toggleClass('d-none d-block');
            $(button).text($(target).hasClass('d-none') ? 'Detalhes' : 'Ocultar');
        });
    });

    // ===============================
    // 🗑️ Modal de Confirmação para Exclusão
    // ===============================

    $('body').on('click', '.link_excluir', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');

        Swal.fire({
            title: 'Você tem certeza?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    // ===============================
    // 👤 Validação do Formulário de Funcionários
    // ===============================

    $('#form-funcionario').submit(function (e) {
        const nome = $('#form-nome').val();
        const ativo = $('#form-ativo').val();
        const cargo = $('#form-cargo').val();
        const nivel = $('#form-nivel').val();
        const credencial = $('#form-credencial').val();
        const senha = $('#form-senha').val();
        const paginasPrivadas = $("input[name='pg_privada_id[]']:checked").length;

        const validators = [
            nome.length >= 1 && nome.length <= 70,
            Number.isInteger(parseInt(ativo)),
            Number.isInteger(parseInt(cargo)),
            Number.isInteger(parseInt(nivel)),
            credencial.length >= 8 && credencial.length <= 20,
            senha.length >= 8 && senha.length <= 64,
            paginasPrivadas > 0,
        ];

        const fields = [
            ['form-nome', '*Preencha este campo. Máximo: 70 caracteres.'],
            ['form-ativo', '*Selecione uma opção.'],
            ['form-cargo', '*Selecione um cargo.'],
            ['form-nivel', '*Selecione um nível.'],
            ['form-credencial', '*Credencial deve ter entre 8 e 20 caracteres.'],
            ['form-senha', '*Senha deve ter entre 8 e 64 caracteres.'],
            ['form-pg-privada-id', '*Selecione pelo menos uma página.'],
        ];

        if (!validateForm(validators, fields)) {
            e.preventDefault();
        }
    });

    // ===============================
    // 💵 Cálculo de Troco no Formulário de Caixa
    // ===============================

    $('#form-valor').on('input', function () {
        const formaPagamento = $('#form-pagamento').val();
        const valorTotal = formatCurrency($('#form-total').val());
        const pagamentoCliente = formatCurrency($(this).val());

        $('#d-form-valor').remove();
        if (formaPagamento === 'Dinheiro' && pagamentoCliente >= valorTotal) {
            const troco = (pagamentoCliente - valorTotal).toFixed(2);
            $('#form-troco').val(
                new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(troco)
            );
            $(this).removeClass('is-invalid');
        } else {
            $(this).addClass('is-invalid');
            $('#form-troco').val('');
            const mensagem = pagamentoCliente < valorTotal ? 'Valor insuficiente.' : 'Forma de pagamento inválida.';
            $(this).after(`<div class="invalid-feedback" id="d-form-valor">${mensagem}</div>`);
        }
    });

    // ===============================
    // 🚫 Confirmação para Cancelamento de Compra
    // ===============================

    $('#cancelarCompra').click(function (e) {
        Swal.fire({
            title: 'Tem certeza?',
            text: 'Deseja cancelar toda a compra?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, cancelar!',
            cancelButtonText: 'Não',
        }).then((result) => {
            if (!result.isConfirmed) {
                e.preventDefault();
            }
        });
    });

})(jQuery);
