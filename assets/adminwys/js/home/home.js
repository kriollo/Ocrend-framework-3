/**
 * Ajax action to api rest
*/
function login(){
    var $ocrendForm = $(this), __data = {};
    $('#login_form').serializeArray().map(function(x){__data[x.name] = x.value;});

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/login",
            dataType: 'json',
            data : __data,
            beforeSend: function(){
                $ocrendForm.data('locked', true)
            },
            success : function(json) {
                if(json.success == 1) {
                   
                  $.alert({
                    icon: "fa fa-success",
                    title: "Alerta",
                    content: json.message,
                    type: "green",
                    typeAnimated: true,
                    autoClose: "ok|3000",
                      buttons: {
                        ok: function() {
                            location.reload();
                        }
                      }
                  });
                } else {
                    $.alert({
                      icon: "fa fa-success",
                      title: "Alerta",
                      content: json.message,
                      type: "orange",
                      typeAnimated: true,
                      autoClose: "ok|3000",
                        buttons: {
                          ok: function() {
                          }
                        }
                    });
                }
            },
            error : function(xhr, status) {
                alert('Ha ocurrido un problema interno');
            },
            complete: function(){
                $ocrendForm.data('locked', false);
            }
        });
    }
}

function recover_pass(){

    $("#cargando").html($("#cargador").html());
    var $ocrendForm = $(this), __data = {};
    $('#lostpass_form').serializeArray().map(function(x){__data[x.name] = x.value;});

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        $.ajax({
            type : "POST",
            url : "api/lostpass",
            dataType: 'json',
            data : __data,
            beforeSend: function(){
                $ocrendForm.data('locked', true)
            },
            success : function(json) {
                if(json.success == 1) {
                   
                  $.alert({
                    icon: "fa fa-success",
                    title: "Alerta",
                    content: json.message,
                    type: "green",
                    typeAnimated: true,
                    autoClose: "ok|3000",
                      buttons: {
                        ok: function() {
                            location.href="home";
                        }
                      }
                  });
                } else {
                    $.alert({
                      icon: "fa fa-success",
                      title: "Alerta",
                      content: json.message,
                      type: "orange",
                      typeAnimated: true,
                      autoClose: "ok|3000",
                        buttons: {
                          ok: function() {
                            $("#cargando").html("");
                          }
                        }
                    });
                }
            },
            error : function(xhr, status) {
                alert('Ha ocurrido un problema interno');
                $("#cargando").html("");
            },
            complete: function(){
                $ocrendForm.data('locked', false);
            }
        });
    }
}

/**
 * Events
 */

$('#login').click(function(e) {
    e.preventDefault;
    login();
});
$('form#login_form input').keypress(function(e) {
    e.preventDefault;
    if(e.which == 13) {
        login();
        return false;
    }
});
$("login_form").submit(function(e){
    e.preventDefault;
});

$('#recover_pass').click(function(e) {
    e.preventDefault;
    recover_pass();
});
$('form#lostpass_form input').keypress(function(e) {
    e.preventDefault;
    if(e.which == 13) {
        recover_pass();
        return false;
    }
});
$("lostpass_form").submit(function(e){
    e.preventDefault;
});