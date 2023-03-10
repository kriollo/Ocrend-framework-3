
'use strict';
import { usePortalStore, pinia } from './portal_STORE.js';

app.use(pinia);
const portalStore = usePortalStore();

app.component('ppal',{
    created() {
        portalStore.getMenu();
    },
    template: `
        <div class="row">
            <div class="col col-md-12 ">
                <h1 class="text-center">Bienvenido al portal de administración</h1>
                <div class="row d-flex justify-content-center">
                    <cardOptions class="col col-md-4 m-1 p-0" v-for="option in menu" :option="option" :key="option.id_menu"></cardOptions>
                </div>
            </div>
        </div>
    `,
    computed: {
        menu() {
            return portalStore.menu;
        },
    }
});

app.component('cardOptions', {
    setup() {
        return {
            mensaje: {}
        }
    },
    props: {
        option: {
            type: Object,
            required: true
        }
    },
    template:`
        <div class="card">
            <div class="card-header text-center bg-danger">
                <span>
                    <i aria-hidden="true" :class="option.glyphicon" class="fa-4x"></i>
                </span>
            </div>
            <div class="card-body text-center">
                <h3>
                    <span>
                        {{ option.menu }}
                    </span>
                </h3>
            </div>
            <div class="card-footer text-center bg-danger p-0">
                <a :href="option.url" class="col col-12 border-0 btn btn-danger"><i aria-hidden="true" class="fas fa-eye"></i></a>
            </div>
        </div>
    `
});

app.mount('.content-wrapper');