'use strict';
export const pinia = Pinia.createPinia();
export const usePerfilesStore = Pinia.defineStore('perfileslStore', {
    state: () => {
        return {
            perfiles: [],
            data_perfil : [],
            urls: [],
            url_inicio: null,
            perfil: "",
        }
    },
    getters: {
        getterDataPerfiles(state) {
            return state.data_perfil;
        }
    },
    actions: {
        getPerfiles: async function() {
            let response = await fetch('api/getPerfiles', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let result = await response.json();
            if(result !== false){
                this.perfiles = result;
            }
        },
        addPerfil: async function(perfil) {
            let response = await fetch('api/new_perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(perfil)
            });
            let result = await response.json();
            if(result !== false){
                this.perfiles.push({nombre: perfil.new_perfil});
            }
        },
        deletePerfil: async function(perfil) {
            let response = await fetch('api/delete_gest_perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(perfil)
            });
            let result = await response.json();
            if(result !== false){
                this.perfiles = this.perfiles.filter(p => p.nombre !== perfil.perfil);
            }
        },
        updatePerfil: async function(perfil) {
            let response = await fetch('api/update_perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(perfil)
            });
            let result = await response.json();
            if(result !== false){
                this.perfiles = this.perfiles.map(p => {
                    if(p.nombre === perfil.old_perfil)
                        p.nombre = perfil.new_perfil;
                    return p;
                });
            }
        },
        getDataPerfiles: async function(perfil) {
            let response = await fetch('api/get_data_perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({perfil: perfil})
            });
            let result = await response.json();
            this.urls = [];this.data_perfil = [];
            if(result !== false){
                for(let i = 0; i < result['result_puro'].length; i++){
                    this.urls.push(result['result_puro'][i]['url']);
                }
                this.data_perfil = result['result_formateado'];
                this.url_inicio = result['url_inicio'];
            }
            return;
        },
        saveOpcionesPerfil: async function(opciones) {
            let response = await fetch('api/update_gest_perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(opciones)
            });
            let result = await response.json();
            return result;
        }
    }
});