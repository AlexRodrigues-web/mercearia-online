$(document).ready(function () {
    // ===============================
    // 🌟 Configuração Padrão para DataTables
    // ===============================

    const dataTableConfig = {
        responsive: true,
        deferRender: true, // Melhora a performance para grandes tabelas
        pagingType: "simple_numbers", // Tipo de paginação
        language: {
            url: window.BASE_URL + "/app/Assets/dataTables/Portuguese-Europe.json", // Usando variável global
        },
    };

    // ===============================
    // 🌟 Função para Iniciar DataTables
    // ===============================
    function iniciarTabela(selector, orderColumn = null) {
        const config = { ...dataTableConfig };
        if (orderColumn !== null) {
            config.order = [[orderColumn, "desc"]];
        }
        $(selector).DataTable(config);
    }

    // ===============================
    // 📊 Iniciar Tabelas com DataTables
    // ===============================

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
