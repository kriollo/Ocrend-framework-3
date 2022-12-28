$("#table_listar_users").dataTable({
    "language": {
        "search": "Buscar:",
        "zeroRecords": "No hay datos para mostrar",
        "info":"Mostrando _END_ Registros, de un total de _TOTAL_ ",
        "loadingRecords": "Cargando...",
        "processing":"Procesando...",
        "infoEmpty":"No hay entradas para mostrar",
        "lengthMenu": "Mostrar _MENU_ Filas",
        "paginate":{
            "first":"Primera",
            "last":"Ultima",
            "next":"Siguiente",
            "previous":"Anterior",
        }
    },
    "autoWidth": true,
    responsive: true
});
$('#btn_reset_pass_user').click(function(e) {
    e.defaultPrevented;
    var $ocrendForm = $(this), __data = {};
    $('#reset_pass_user_form').serializeArray().map(function(x){__data[x.name] = x.value;});

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/resetpass",
            dataType: 'json',
            data : __data,
            beforeSend: function(){
                $ocrendForm.data('locked', true)
            },
            success : function(json) {
                if(json.success == 1) {
                    $(document).Toasts('create', {
                        class: 'bg-success', 
                        title: 'Success',
                        subtitle: json.title,
                        body: json.message,
                        autohide: true,
                        delay: 5000
                    })
                    $('#modal_reset_pass_user').modal('hide');
                } else {
                    $(document).Toasts('create', {
                        class: 'bg-warning', 
                        title: 'Alerta',
                        subtitle: json.title,
                        body: json.message,
                        autohide: true,
                        delay: 5000
                    })
                }
            },
            error : function(xhr, status) {
                $(document).Toasts('create', {
                    class: 'bg-danger', 
                    title: 'Warning',
                    subtitle: 'Error',
                    body: xhr.responseText,
                    autohide: true,
                    delay: 5000
                })
            },
            complete: function(){
                $ocrendForm.data('locked', false);
            }
        });
    }
});
function carga_modal_reset_pass(id_user){
    $('#modal_reset_pass_user').modal('show');
    document.getElementById('id_user').value=id_user
}
function carga_modal_select_campus_user(id_user){
    var FormD = new FormData();
    FormD.append("id_user", id_user);
    $.ajax({
        type : "POST",
        url : "api/load_modal_campus_usuario",
        contentType:false,
        processData:false,
        data : FormD,
        success : function(json) {
            $("#carga_select_campus").html(json.html)
            $('#modal_select_campus_user').modal('show');                
            document.getElementById('input_id_user_select_campus_user').value=id_user
        },
        error : function(xhr, status) {
            $(document).Toasts('create', {
                class: 'bg-danger', 
                title: 'Warning',
                subtitle: 'Error',
                body: xhr.responseText,
                autohide: true,
                delay: 5000
            })
        }
    });
}
$('#btn_select_campus_user').click(function(e) {
    e.defaultPrevented;


    var FormD = new FormData();
    FormD.append("id_user", $("#input_id_user_select_campus_user").val());
    
    var arrCampus = new Array();
    $("#selcampus option").each(function(){
        if($(this).is(":selected")){
            arrCampus.push($(this).attr('value'));
        }
    });
        
    FormD.append("campus", arrCampus);
    
    var $ocrendForm = $(this), __data = {};
    $('#select_campus_user_form').serializeArray().map(function(x){__data[x.name] = x.value;});
    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/update_modal_campus_usuario",
            contentType:false,
            processData:false,
            data : FormD,
            beforeSend: function(){
                $ocrendForm.data('locked', true)
            },
            success : function(json) {
                if(json.success == 1) {
                    $(document).Toasts('create', {
                        class: 'bg-success', 
                        title: 'Success',
                        subtitle: json.title,
                        body: json.message,
                        autohide: true,
                        delay: 5000
                    })
                    $('#modal_select_campus_user').modal('hide');
                } else {
                    $(document).Toasts('create', {
                        class: 'bg-warning', 
                        title: 'Alerta',
                        subtitle: json.title,
                        body: json.message,
                        autohide: true,
                        delay: 5000
                    })
                }
            },
            error : function(xhr, status) {
                $(document).Toasts('create', {
                    class: 'bg-danger', 
                    title: 'Warning',
                    subtitle: 'Error',
                    body: xhr.responseText,
                    autohide: true,
                    delay: 5000
                })
            },
            complete: function(){
                $ocrendForm.data('locked', false);
            }
        });
    }
});