$(document).ready(function () {
   
    const dataTableConfig = {
        responsive: true,
        deferRender: true, 
        pagingType: "simple_numbers", // Tipo de paginação
        language: {
            url: window.BASE_URL + "/app/Assets/dataTables/Portuguese-Europe.json", // Usando variável global
        },
    };

    function iniciarTabela(selector, orderColumn = null) {
        const config = { ...dataTableConfig };
        if (orderColumn !== null) {
            config.order = [[orderColumn, "desc"]];
        }
        $(selector).DataTable(config);
    }


    // Produtos
    iniciarTabela("#listar-produtos", 8);

    // Fornecedores
    iniciarTabela("#listar-fornecedores", 3);

    // Estoque
    iniciarTabela("#listar-estoque");

    // Cargos
    iniciarTabela("#listar-cargos");

    // Níveis
    iniciarTabela("#listar-nivel");

    // Páginas Públicas
    iniciarTabela("#listar-paginas-publicas", 2);

    // Páginas Privadas
    iniciarTabela("#listar-paginas-privadas", 2);

    // Funcionários
    iniciarTabela("#listar-funcionarios", 5);
});
