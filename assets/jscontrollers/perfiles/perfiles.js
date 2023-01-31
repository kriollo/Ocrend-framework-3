'use strict';

import { usePerfilesStore, pinia } from './perfiles_STORE.js';
app.use(pinia);
const perfilesStore = usePerfilesStore();

app.component('perfilppal', {
    template:`
        <div class="row">
            <div class="col col-md-4">
                <perfiles></perfiles>
            </div>
            <div class="col col-md-8">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Seleccione Opciones del Perfil</h3>
                    </div>
                    <div class="card-body">
                        <opcionesmenu></opcionesmenu>
                    </div>
                </div>
            </div>
        </div>
    `,
});
app.component('perfiles', {
    data() {
        return {
            showNewPerfil: false,
            perfil: ""
        };
    },
    created() {
        perfilesStore.getPerfiles();
    },
    computed:{
        perfiles(){
            return perfilesStore.perfiles;
        },
        modeView(){
            return localStorage.getItem('modeView');
        },
        perfilActivo(){
            return perfilesStore.perfil;
        }
    },
    methods: {
        seleccionarPerfil(nombre){
            perfilesStore.perfil = nombre;
            perfilesStore.getDataPerfiles(nombre);
        },
        showNewPerfilForm(mode){
            this.perfil = "";
            if(mode == 'edit')
                this.perfil = this.perfilActivo;

            this.showNewPerfil = !this.showNewPerfil;
        },
        deletePerfil(nombre){
            const perfil = {
                perfil : nombre
            };
            perfilesStore.deletePerfil(perfil);
            perfilesStore.data_perfil = [];
        }
    },
    template:`
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Perfil</h3>
                <editperfil v-if="showNewPerfil" :perfil_input="perfil" v-on:cancelar="showNewPerfilForm"></editperfil>
                <div class="card-tools">
                    <button v-if="!showNewPerfil" type="button" class="btn btn-tool" @click="showNewPerfilForm('new')"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <table class="table">
                        <tbody>
                            <tr v-for="perfil in perfiles">
                                <td style="width:80%" >
                                    <li  class="list-group-item list-group-item-action list-group-item-info" :class="{'active': perfilActivo == perfil.nombre, 'text-white': modeView === 'dark', 'text-black': modeView !== 'dark' }"  @click="seleccionarPerfil(perfil.nombre)" >{{ perfil.nombre }}</li>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-tool" @click="seleccionarPerfil(perfil.nombre);showNewPerfilForm('edit')"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-tool" @click="deletePerfil(perfil.nombre)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </ul>
            </div>
        </div>
    `
});
app.component('editperfil', {
    props:{
        perfil_input: {
            type: String,
            required: false,
            default: ''
        }
    },
    data(){
        return {
            perfil: this.perfil_input
        }
    },
    methods: {
        guardarPerfil(){
            const new_perfil = {
                old_perfil : this.perfil_input,
                new_perfil : this.perfil
            };
            if(this.perfil_input != '')
                perfilesStore.updatePerfil(new_perfil);
            else
                perfilesStore.addPerfil(new_perfil);

            this.$emit('cancelar','');
        },
        cancelar(){
            this.$emit('cancelar','');
        }
    },
    template:`
        <div class="form-group">
            <label for="nombre" class="class1">: Ingrese Nombre</label>
            <input type="text" class="form-control" id="nombre" placeholder="Nombre del Perfil" v-model="perfil">
            <div class="d-flex align-content-center">
                <button type="button" class="btn btn-primary" @click="guardarPerfil">Guardar</button>
                <button type="button" class="btn btn-danger" @click="cancelar">Cancelar</button>
            </div>
        </div>
    `
});
app.component('opcionesmenu', {
    setup(){
        perfilesStore.getDataPerfiles('a99');
    },
    watch:{
        url_inicio(){
            this.url_local = this.url_inicio;
        }
    },
    computed:{
        dataPerfil(){
            return perfilesStore.getterDataPerfiles;
        },
        urls(){
            return perfilesStore.urls;
        },
        perfilActivo(){
            return perfilesStore.perfil;
        },
        url_inicio(){
            return perfilesStore.url_inicio;
        }
    },
    data(){
        return {
            url_local: ''
        }
    },
    methods: {
        saveOpcionesPerfil(){
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
            const opciones = {
                perfil: this.perfilActivo,
                url_inicio: this.url_local,
                data_perfil: this.dataPerfil
            };
            perfilesStore.saveOpcionesPerfil(opciones).
            then(json => {
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
            });
        },
        validatedChecked(){
            let checked = false;
            for (const key in this.dataPerfil) {
                if (this.dataPerfil.hasOwnProperty(key)) {
                    const element = this.dataPerfil[key];
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
    template:`
        <div class="row">
            <div class="col col-md-6">
                <div class="form-group">
                    <label for="pagina_inicio">Seleccione página de Inicio</label>
                    <select class="form-control" id="pagina_inicio" v-model="url_local">
                        <option value="portal">home</option>
                        <option v-for="url in urls" :value="url">{{ url }}</option>
                    </select>
                </div>
            </div>
            <div class="col col-md-6 d-flex text-center float-right">
                <button v-if="perfilActivo != ''" type="button" class="btn btn-success" @click="saveOpcionesPerfil">Guardar</button>
            </div>
        </div>
        <hr>
        <label>Seleccione las opciones que tendrá acceso</label>
        <div v-for="opcion in dataPerfil">
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
    `
});

app.mount('.content-wrapper');