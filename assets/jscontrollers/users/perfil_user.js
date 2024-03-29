function updateAvatar(){
    var FormD = new FormData();
    FormD.append("id_user",$("#id_user").val());
    FormD.append("avatar", document.getElementById('file_avatar').files[0]);

    $.ajax({
        type: "POST",
        url : 'api/updateAvatar',
        contentType:false,
        processData:false,
        data : FormD,
        success : function(json) {
            if (json.success == 1){
                $(document).Toasts('create', {
                    class: 'bg-success', 
                    title: 'Success',
                    subtitle: '',
                    body: json.message,
                    autohide: true,
                    delay: 5000
                })
                setTimeout(function(){
                    location.reload();
                },1000);
            }else{
                $(document).Toasts('create', {
                    class: 'bg-warning', 
                    title: 'Alerta',
                    subtitle: '',
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
                subtitle: '',
                body: xhr.responseText,
                autohide: true,
                delay: 5000
            })
        }
    });
}
function resetpass(){
    var FormD = new FormData();
    FormD.append("id_user",$("#id_user").val())
    FormD.append("pass_new",$("#pass_new").val())
    FormD.append("repass_new",$("#repass_new").val())
    

    $.ajax({
        type: "POST",
        url : 'api/resetpass',
        contentType:false,
        processData:false,
        data : FormD,
        success : function(json) {
            if (json.success == 1){
                $(document).Toasts('create', {
                    class: 'bg-success', 
                    title: 'Success',
                    subtitle: '',
                    body: json.message,
                    autohide: true,
                    delay: 5000
                })
                setTimeout(function(){
                    location.reload();
                },1000);
            }else{
                $(document).Toasts('create', {
                    class: 'bg-warning', 
                    title: 'Alerta',
                    subtitle: '',
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
                subtitle: '',
                body: xhr.responseText,
                autohide: true,
                delay: 5000
            })
        }
    });
}

function updateModo(){
    var mode = $("#updatemodeselect").val();
    console.log("🚀 ~ file: perfil_user.js:100 ~ mode", mode)
    
    //save in local store the new mode
    localStorage.setItem('modoViewTemplate',mode);
    //reload page
    location.reload();
}

// revisar cuando el documento este listo y seleccionar el modo de vista en el select
$(document).ready(function(){
    var modoViewTemplate = localStorage.getItem('modoViewTemplate');
    if(modoViewTemplate != null){
        $("#updatemodeselect").val(modoViewTemplate);
    }
});