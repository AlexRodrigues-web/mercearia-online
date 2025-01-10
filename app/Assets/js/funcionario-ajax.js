function dadosCadastro()
{
    $.post("http://localhost/mercearia/funcionario/funcionarioAjax",function(result){
    try{
        let objeto = JSON.parse(result);
        let text = "";

        function listar(item)
        {
            text +="<option value='" + item['id'] +"' >" + item['nome'] + "</option>"; 
        }
        function checkmultiplo(item)
        {
            text += "<div class='form-check'><input type = 'checkbox' id = 'pagina"+item['id']+"' name = 'pg_privada_id[]' value = '"+item['id']+"'class='form-check-input'";
            if(item['nome'] == 'Home'){text+='checked'};
            text+="><label for = 'pagina"+item['id']+"'class='form-check-label'>"+item['nome']+"</label></div>";
        }

       
        objeto['cargo'].forEach(listar);
        $("#form-cargo").append(text);

        text = "";
        objeto['nivel'].forEach(listar);
        $("#form-nivel").append(text);

        text = "";
        objeto['pagina'].forEach(checkmultiplo);
        $("#form-pg-privada-id").append(text);

    }
    catch(err){ 
        // encaminhar para a página home exibindo um alerta sobre o erro
        window.location.replace("http://localhost/mercearia/home");
    }
    })
    
} 

function dadosAtualizar()
{
    let id = $("input[type='hidden'][name='id']").val();
    let cargo = $("#option_padrao_cargo").val();
    let nivel = $("#option_padrao_nivel").val();
   
    $.post("http://localhost/mercearia/funcionario/funcionarioDetalhesAjax",{id:id},function(result){
        
        try{
            let objeto = JSON.parse(result);
            let text = "";
            let campo = cargo;
            let id_paginas_funcionario=[];

            for(let i = 0; i <objeto['f_paginas'].length; i++)
            {
                id_paginas_funcionario.push(objeto['f_paginas'][i]['f_paginas_id']);
            }

            function listar(item)
            {
                if(item['id'] != campo){
                    text +="<option value=" + item['id'] + ">" + item['nome'] + "</option>"; 
                }
            }

            function checkmultiplo(item)
            {
                text += "<div class='form-check'><input type = 'checkbox' id = 'pagina"+item['id']+"' name = 'pg_privada_id[]' value = '"+item['id']+"'class='form-check-input'";
            
                if(id_paginas_funcionario.includes(item['id'])){text+='checked'};
                text+="><label for = 'pagina"+item['id']+"'class='form-check-label'>"+item['nome']+"</label></div>";
            
            }

            objeto['cargo'].forEach(listar);
            $("#form-cargo").append(text);

            text = "";
            campo = nivel;
            objeto['nivel'].forEach(listar);
            $("#form-nivel").append(text);

            text= "";
            objeto['paginas'].forEach(checkmultiplo);
            $("#form-pg-privada-id").append(text);

        }
        catch(err){ 
            // encaminhar para a página home exibindo um alerta sobre o erro
            window.location.replace("http://localhost/mercearia/home");
        }
    })
    
} 


