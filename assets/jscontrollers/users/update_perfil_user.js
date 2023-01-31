"use strict";

app.component('userperfilppal', {
    data() {
        return {
            id_user: parseInt(id_user),
        };
    },
    template:`
        <div class="row">
            <div class="col col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Seleccione Opciones del Perfil</h3>
                    </div>
                    <div class="card-body">
                        <userperfil
                            :id_user="id_user"
                        ></userperfil>
                    </div>
                </div>
            </div>
        </div>
    `,
});
app.component('userperfil', {
    props:{
        id_user: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            perfil: [],
        }
    },
    created() {
        this.getPerfil();
    },
    methods: {
        getPerfil: async function() {
            let response = await fetch('api/getPerfilUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({id_user: this.id_user})
            });
            let result = await response.json();
            if(result !== false){
                this.perfil = result['result_formateado'];
            }
        },
        savePerfilDefinido: async function (){
            if(this.validatedChecked() !== true){
                $(document).Toasts('create', {
                    class: 'bg-warning',
                    title: 'Alerta',
                    subtitle: 'Error',
                    body: 'Debe seleccionar al menos una opcion',
                    autohide: true,
                    delay: 5000
                });
                return;
            }
            const response = await fetch('api/updatePerfilUsuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_user: this.id_user,
                    perfil: this.perfil
                })
            });
            const json = await response.json();
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
                });
            }
        },
        validatedChecked(){
            let checked = false;
            for (const key in this.perfil) {
                if (this.perfil.hasOwnProperty(key)) {
                    const element = this.perfil[key];
                    element['submenu'].forEach(sub => {
                        if(sub['checked'] == true){
                            checked = true;
                        }
                    });
                }
            }
            return checked;
        }
    },
    template: `
        <div class="col col-md-4 offset-md-4">
            <button type="button" class="btn btn-success" @click="savePerfilDefinido">Guardar Cambios</button>
            <hr>
            <div v-for="opcion in perfil">
                <i :class="opcion.icon" class='nav-icon'></i>
                <label><span class="ml-2">{{ opcion.menu }}</span></label>
                <ul class="list-group list-group-flush">
                    <li v-for="subopcion in opcion.submenu" class="list-group-item d-flex align-items-baseline">
                        <i class="fa fa-angle-right"></i>
                        <div class="icheck-success ml-2">
                            <input type="checkbox" :id="opcion.id_menu+'-'+subopcion.id_submenu" v-model="subopcion.checked">
                            <label :for="opcion.id_menu+'-'+subopcion.id_submenu"><span>{{ subopcion.descripcion }}</span></label>
                        </div>
                    </li>
                </ul>
            </div>
            <hr>
            <button type="button" class="btn btn-success" @click="savePerfilDefinido">Guardar Cambios</button>
        </div>
    `
});

app.mount('.content-wrapper');