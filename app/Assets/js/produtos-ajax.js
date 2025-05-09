function carregarFornecedores(url, fornecedor_id = null) {
    $.ajax({
        url: url,
        method: "POST",
        contentType: "application/json",
        success: function (result) {
            try {
                let fornecedores = JSON.parse(result);

                if (fornecedores.length === 0) {
                    Swal.fire("Aviso", "Nenhum fornecedor encontrado. Por favor, cadastre fornecedores.", "info");
                    return;
                }

                let options = fornecedores
                    .filter(item => item.id !== fornecedor_id)
                    .map(item => `<option value="${item.id}">${escaparHTML(item.nome)}</option>`)
                    .join("");

                $("#form-fornecedor_id").html(options);

            } catch (error) {
                Swal.fire("Erro", "Erro ao processar os dados do fornecedor. Tente novamente.", "error");
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: "Erro ao carregar dados!",
                text: `Código ${xhr.status}: ${xhr.statusText}`,
                icon: "error",
                showCancelButton: true,
                confirmButtonText: "Recarregar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.reload();
                }
            });
        }
    });
}

// Função para escapar HTML e prevenir XSS
function escaparHTML(texto) {
    let div = document.createElement("div");
    div.innerText = texto;
    return div.innerHTML;
}

// Função para editar produtos
function editarProdutos() {
    let fornecedor_id = $("#option_padrao").val();
    carregarFornecedores("/mercearia/produtos/fornecedorAjax", fornecedor_id);
}

// Função para listar produtos (index)
function indexProdutos() {
    carregarFornecedores("/mercearia/produtos/fornecedorAjax");
}
