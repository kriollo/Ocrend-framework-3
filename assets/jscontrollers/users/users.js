"use strict";
import { usePerfilesStore, pinia } from '../perfiles/perfiles_STORE.js';
app.use(pinia);
const perfilesStore = usePerfilesStore();

app.component('Userppal', {
    data() {
        return {
            mode: id_user == "" ? "new" : "edit",
        };
    },
    template:`
        <div class="col col-md-12">
            <user_panel
                :mode="mode"
            ></user_panel>
        </div>
    `,
});
app.component('user_panel', {
    props:{
        mode : {
            type: String,
            requiered: true
        }
    },
    data() {
        return {
            id_user: id_user,
            user_data:{
                id_user: id_user,
                name: "",
                email: "",
                perfil: "",
                pagina_inicio: "portal",
                rol: 0,
                pass: "",
                pass_repeat: "",
            },
            paginas: [],
        }
    },
    computed:{
        perfiles: function(){
            return perfilesStore.perfiles;
        },
        urls: function(){
            return perfilesStore.urls;
        }
    },
    created: function(){
        let self = this;
        if (self.id_user != "" && self.id_user != "undefined") {
            self.getUserData(self.id_user);
        }

        perfilesStore.getPerfiles();
    },
    methods: {
        getUserData: function(id_user){
            let self = this;
            $.ajax({
                type : "POST",
                url : "api/getUserByIdPOST",
                data : {
                    id_user: id_user
                },
                success : function(response) {
                    if(response !== false){
                        self.user_data = response;
                        self.user_data['rol'] = self.user_data['rol'] == 1 ? true : false;
                        perfilesStore.getDataPerfiles(self.user_data['perfil']);
                    }
                },
                error : function(xhr, status) {
                    alert('Ha ocurrido un problema interno ' + xhr.responseText);
                }
            });
        },
        getDataPerfil: function(){
            perfilesStore.getDataPerfiles(this.user_data.perfil).
            then((data) => {
                let url = 'portal';
                this.perfiles.forEach((p) => {
                    if(p.nombre == this.user_data.perfil){
                        url = p.url;
                    }
                });
                this.user_data.pagina_inicio = url;
            });
        },
        saveUser: function(){
            const self = this;
            let url = "";
            if(self.mode == "edit"){
                url = "api/updateuser";
            }else{
                url = "api/registeruser";
            }
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    mode: self.mode,
                    user_data: self.user_data
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
                    alert('Ha ocurrido un problema interno ' + xhr.responseText);
                }
            });
        }
    },
    template: `
        <div class="card card-outline" :class="mode == 'new' ? 'card-primary':'card-info' ">
            <div class="card-header">
                <h3 class="card-title">Nuevo Usuario</h3>
            </div>
            <div class="card-body">
                <div class="col-md-4 offset-md-4">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control" id="name" v-model="user_data.name" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" v-model="user_data.email" placeholder="Email" :disabled="mode == 'edit' ? true:false">
                    </div>
                    <div class="form-group" v-if="mode == 'new'">
                        <label for="pass">Password</label>
                        <input class="form-control" id="pass"        type="password" placeholder="Password" required autocomplete="new-password" v-model="user_data.pass"/>
                        <input class="form-control" id="pass_repeat" type="password" placeholder="Repita Password" required autocomplete="new-password" v-model="user_data.pass_repeat"/>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="perfil">Perfil</label>
                        <select class="form-control" id="perfil" v-model="user_data.perfil" @change="getDataPerfil">
                            <option>--</option>
                            <option>DEFINIDO</option>
                            <option v-for="item in perfiles">{{item.nombre}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pagina_inicio">Pagina de Inicio</label>
                        <select class="form-control" id="pagina_inicio" v-model="user_data.pagina_inicio" >
                            <option value="portal">home</option>
                            <option v-for="pagina in urls" :value="pagina">{{pagina}}</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="icheck-warning">
                            <input type="checkbox" id="rol" v-model="user_data.rol">
                            <label for="rol">
                                Asignar Rol Administrador
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="col col-md-4 offset-md-4">
                    <button type="button" class="btn btn-primary" @click="saveUser">Guardar</button>
                </div>
            </div>
        </div>
    `,
});

app.mount('.content-wrapper');