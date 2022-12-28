$('#update_perfil_user').click(function(e) {
    e.defaultPrevented;
    var $ocrendForm = $(this), __data = {};
    $('#form_user_perfil').serializeArray().map(function(x){__data[x.name] = x.value;});

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/update_perfil_usuario",
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
                        location.href = "users/usuarios";
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