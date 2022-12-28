'use strict';
export const store = new Vuex.Store({
    state: {
        menu: []
    },
    mutations: {
        SET_MENU(state, menu) {
            state.menu = menu;
        }
    },
    actions: {
        getMenu: async function({ commit }) {
            let response = await fetch('api/get_menu_user_by_POST', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
            });
            let result = await response.json();
            let menu = result;
            if (menu != false) {
                menu = menu.reverse();
                const MenuUnique = GetUniquedArrayObject('id_menu', menu);
                MenuUnique.reverse();
                commit('SET_MENU', MenuUnique);
            }
        }
    }
});