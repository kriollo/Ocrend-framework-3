
var app = new Vue({ 
    el: '.content',
    delimiters: ['${', '}'],
    data: { 
        array_user: [],
        array_perfiles: [],
        name: "",
        email: "",
        perfil: "",
        pagina_inicio: "",
        rol: ""

    },
    created: function(){
        var self = this;


        // Carga Perfiles
        self.array_perfiles = [{
            text: "--",
            value: "--"
        },{
            text: "DEFINIDO",
            value: "DEFINIDO"
        }]; 
        $.ajax({
            type : "POST",
            url : "api/getPerfiles",
            success : function(response) {
                $.each(response, function (index, value) {
                    self.array_perfiles.push({
                        text: value.nombre,
                        value: value.nombre
                    });
                });
            },
            error : function(xhr, status) {
                alert('Ha ocurrido un problema interno ' + xhr.responseText);
            }
        });

    },
    mounted : function() {
        var self = this;
 
        // Buscar Usuario a editar
        if ($("#id_user").val() != "" && $("#id_user").val() != "undefined") {
            var FormD = new FormData();
            FormD.append("id_user", $("#id_user").val());
            $.ajax({
                type : "POST",
                url : "api/getUserByIdPOST",
                contentType:false,
                processData:false,
                data : FormD,
                success : function(response) {  
                    self.name = response['name'];
                    self.email = response['email'];
                    self.perfil = response['perfil'];
                    self.pagina_inicio = response['pagina_inicio'];
                    if (response['rol'] == 1) {
                        self.rol = true    
                    }
                },
                error : function(xhr, status) {
                    alert('Ha ocurrido un problema interno ' + xhr.responseText);
                }
            });     
        }
    },
    methods: {
        registeruser: function () {
            var FormD = new FormData();
            FormD.append("name", $("#name").val());
            FormD.append("email", $("#email").val());
            FormD.append("pass", $("#pass").val());
            FormD.append("pass_repeat", $("#pass_repeat").val());
            FormD.append("perfil", $("#perfil").val());
            FormD.append("pagina_inicio",$("#pagina_inicio").val());
            FormD.append("rol", $("#rol").is(":checked")?1:0);

            var $ocrendForm = $(this), __data = {};
            $('#register_user_form').serializeArray().map(function(x){__data[x.name] = x.value;});

            if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
                $.ajax({
                    type : "POST",
                    url : "api/registeruser",
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
                            });
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
                            });
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
                        });
                    },
                    complete: function(){
                        $ocrendForm.data('locked', false);
                    }
                });
            }
        },
        updateuser: function (){
            var FormD = new FormData();
            FormD.append("id_user", $("#id_user").val());
            FormD.append("name", $("#name").val());
            FormD.append("email", $("#email").val());
            FormD.append("pass", $("#pass").val());
            FormD.append("pass_repeat", $("#pass_repeat").val());
            FormD.append("perfil", $("#perfil").val());
            FormD.append("pagina_inicio",$("#pagina_inicio").val());
            FormD.append("rol", $("#rol").is(":checked")?1:0);

            var $ocrendForm = $(this), __data = {};
            $('#register_user_form').serializeArray().map(function(x){__data[x.name] = x.value;});

            if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
                $.ajax({
                    type : "POST",
                    url : "api/updateuser",
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
                            });
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
                            });
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
                        });
                    },
                    complete: function(){
                        $ocrendForm.data('locked', false);
                    }
                });
            }
        }
    }
})

