function escaparHTML(texto) {
    let div = document.createElement("div");
    div.innerText = texto;
    return div.innerHTML;
}

function preencherFormulario(url, idFuncionario = null, cargoAtual = null, nivelAtual = null) {
    $.post(url, idFuncionario ? { id: idFuncionario } : {}, function (result) {
        try {
            let objeto = JSON.parse(result);

            if (!objeto || typeof objeto !== 'object') {
                throw new Error('Dados inválidos recebidos.');
            }

            // Função para montar as opções do select
            function montarOpcoes(item, campoAtual) {
                if (item.id != campoAtual) {
                    return `<option value="${item.id}">${escaparHTML(item.nome)}</option>`;
                }
                return '';
            }

            // Função para montar os checkboxes das páginas privadas
            function montarCheckbox(item, paginasSelecionadas) {
                const isChecked = paginasSelecionadas.includes(item.id) ? 'checked' : '';
                return `
                    <div class='form-check'>
                        <input type='checkbox' id='pagina${item.id}' name='pg_privada_id[]' value='${item.id}' class='form-check-input' ${isChecked}>
                        <label for='pagina${item.id}' class='form-check-label'>${escaparHTML(item.nome)}</label>
                    </div>
                `;
            }

            // Cargos
            let cargosHtml = objeto.cargo ? objeto.cargo.map(item => montarOpcoes(item, cargoAtual)).join('') : '';
            $("#form-cargo").html(cargosHtml);

            // Níveis
            let niveisHtml = objeto.nivel ? objeto.nivel.map(item => montarOpcoes(item, nivelAtual)).join('') : '';
            $("#form-nivel").html(niveisHtml);

            // Páginas Privadas
            let paginasSelecionadas = idFuncionario && objeto.f_paginas ? objeto.f_paginas.map(p => p.f_paginas_id) : [];
            let paginasHtml = objeto.paginas ? objeto.paginas.map(item => montarCheckbox(item, paginasSelecionadas)).join('') : '';
            $("#form-pg-privada-id").html(paginasHtml);

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao carregar os dados',
                text: 'Houve um erro ao processar as informações. Tente novamente.',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.location.replace(window.BASE_URL + "/home");
            });
        }
    }).fail(function () {
        Swal.fire({
            icon: 'error',
            title: 'Erro de Conexão',
            text: 'Não foi possível carregar os dados. Verifique sua conexão.',
            confirmButtonText: 'Recarregar',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            location.reload();
        });
    });
}

// Função para preencher os dados de cadastro de funcionários
function dadosCadastro() {
    preencherFormulario(window.BASE_URL + "/funcionario/funcionarioAjax");
}

// Função para preencher os dados de atualização de funcionários
function dadosAtualizar() {
    let id = $("input[type='hidden'][name='id']").val();
    let cargoAtual = $("#option_padrao_cargo").val();
    let nivelAtual = $("#option_padrao_nivel").val();

    preencherFormulario(window.BASE_URL + "/funcionario/funcionarioDetalhesAjax", id, cargoAtual, nivelAtual);
}
