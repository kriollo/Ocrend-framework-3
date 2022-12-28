function mostar_datos_perfil(){

    var FormD = new FormData();
    FormD.append("id", $("#select_perfil").val());
    $.ajax({
        type : 'POST',
        url : 'api/get_data_perfil',
        contentType:false,
        processData:false,
        data : FormD,
        success : function(json) {
            $("#perfil").val($("#select_perfil").val())     
            if(json.success == 1) {       
                
                $.each(json.data, function (index, value) {
                    if (value['checked'] == 1)
                        $('#check-'+value['id_menu']+'-'+value['id_submenu']).prop('checked', true);
                    else
                    $('#check-'+value['id_menu']+'-'+value['id_submenu']).prop('checked', false);
                });
                
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
        }
    });
}
$('#btn_new_perfil').click(function(e) {
    e.defaultPrevented;

    var FormD = new FormData();
    FormD.append("new_perfil", $("#new_perfil").val());
   
    var $ocrendForm = $(this), __data = {};
    $('#new_perfil_form').serializeArray().map(function(x){__data[x.name] = x.value;});
    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/new_perfil",
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
                    setTimeout(function(){
                        location.reload();
                    },1000);
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
$('#update_get_perfil').click(function(e) {
    e.defaultPrevented;
    var $ocrendForm = $(this), __data = {};
    $('#form_gest_perfil').serializeArray().map(function(x){__data[x.name] = x.value;});

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/update_gest_perfil",
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
                    setTimeout(function(){
                        location.reload();
                    },1000);
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
$('#deleteperfil').click(function(e) {
    e.defaultPrevented;
    var FormD = new FormData();
    FormD.append("id", $("#select_perfil").val());

    $.ajax({
        type : "POST",
        url : "api/delete_gest_perfil",
        contentType:false,
        processData:false,
        data : FormD,
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
                setTimeout(function(){
                    location.reload();
                },1000);
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
        }
    });
});