'use strict';
export const pinia = Pinia.createPinia();
export const usePortalStore = Pinia.defineStore('portalStore', {
    state: () => {
        return {
            menu: [],
        }
    },
    getters: {
        gettMenu: (state) => {
            return state.menu;
        }
    },
    actions: {
        getMenu: async function() {
            let response = await fetch('api/get_menu_user_by_POST', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let result = await response.json();
            let menu = result;
            if (menu != false) {
                menu = menu.reverse();
                const MenuUnique = GetUniquedArrayObject('id_menu', menu);
                MenuUnique.reverse();
                this.menu = MenuUnique;
            }
            return true;
        }
    }
});