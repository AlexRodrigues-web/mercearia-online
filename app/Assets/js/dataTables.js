$(document).ready(function() {
    $('#listar-produtos').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order":[[8,'desc']]
    });
    $('#listar-fornecedores').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order":[[3,'desc']]
    });
    $('#listar-estoque').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        }
    });
    $('#listar-cargos').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        }
    });
    $('#listar-nivel').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        }
    });
    $('#listar-paginas-publicas').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order":[[2,'desc']]
    });
    $('#listar-paginas-privadas').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order":[[2,'desc']]
    });
    $('#listar-funcionarios').DataTable( {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        "order":[[5,'desc']]
    });
});
