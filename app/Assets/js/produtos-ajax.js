function editarProdutos()
{
    let fornecedor_id = $("#option_padrao").val();

    $.post("http://localhost/mercearia/produtos/fornecedorAjax",function(result){
        let objeto = JSON.parse(result);
        let text = "";

        objeto.forEach(listar);

        $selected = $('#form_option').val();

        function listar(item)
        {
        if(item['id'] != fornecedor_id){
            text +="<option value=" + item['id'] + ">" + item['nome'] + "</option>"; 
        }
        }
        
        $("#form-fornecedor_id").append(text);
    })

    .fail(function(){
        if(confirm('Ops, não foi possível carregar todos os dados, por favor Recarregue a página novamente!'))
        {
            document.location.reload();
        }        
    });
}
    
function indexProdutos()
{
    $.post("http://localhost/mercearia/produtos/fornecedorAjax",function(result){
        let objeto = JSON.parse(result);
        let text = "";

        objeto.forEach(listar);

        function listar(item)
        {
            text +="<option value=" + item['id'] + ">" + item['nome'] + "</option>"; 
        }
        
        $("#form-fornecedor_id").append(text);
    })
    
    .fail(function(){
        if(confirm('Ops, não foi possível carregar todos os dados, por favor Recarregue a página novamente!'))
        {
            document.location.reload();
        }        
    });
    

} 


